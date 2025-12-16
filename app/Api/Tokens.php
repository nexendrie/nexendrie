<?php
declare(strict_types=1);

namespace Nexendrie\Api;

use Nette\Security\User;
use Nette\Utils\Random;
use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Orm\ApiToken;
use Nexendrie\Orm\Model as ORM;

final class Tokens
{
    public function __construct(
        private readonly int $ttl,
        public readonly int $length,
        private readonly ORM $orm,
        private readonly User $user
    ) {
    }

    public function create(): ApiToken
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }

        /** @var \Nexendrie\Orm\User $user */
        $user = $this->orm->users->getById($this->user->id);
        if (!$user->api) {
            throw new ApiNotEnabledException();
        }

        $token = new ApiToken();
        $token->user = $user;
        $token->token = Random::generate(max(1, $this->length));
        $token->expire = time() + $this->ttl;
        $this->orm->apiTokens->persistAndFlush($token);

        return $token;
    }

    public function invalidate(string $token): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }

        $tokenEntity = $this->orm->apiTokens->getByToken($token);
        if ($tokenEntity === null || $tokenEntity->user->id !== $this->user->id) {
            throw new TokenNotFoundException();
        }

        $dt = new \DateTime();
        if ($tokenEntity->expire <= $dt->getTimestamp()) {
            throw new TokenExpiredException();
        }

        $tokenEntity->expire = $dt->getTimestamp();
        $this->orm->apiTokens->persistAndFlush($tokenEntity);
    }
}
