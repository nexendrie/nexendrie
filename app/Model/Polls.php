<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Poll as PollEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Polls Model
 *
 * @author Jakub Konečný
 * @property-write  \Nette\Security\User $user
 */
final class Polls
{
    use \Nette\SmartObject;

    private \Nette\Security\User $user;

    public function __construct(private readonly ORM $orm)
    {
    }

    protected function setUser(\Nette\Security\User $user): void
    {
        $this->user = $user;
    }

    /**
     * Get list of all polls
     *
     * @return PollEntity[]|ICollection
     */
    public function all(): ICollection
    {
        return $this->orm->polls->findAll();
    }

    /**
     * Show specified poll
     *
     * @throws PollNotFoundException
     */
    public function view(int $id): PollEntity
    {
        $poll = $this->orm->polls->getById($id);
        return $poll ?? throw new PollNotFoundException("Specified poll does not exist.");
    }

    /**
     * Add poll
     *
     * @throws AuthenticationNeededException
     * @throws MissingPermissionsException
     */
    public function add(array $data): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException("This action requires authentication.");
        }
        if (!$this->user->isAllowed("poll", "add")) {
            throw new MissingPermissionsException("You don't have permissions for adding news.");
        }
        $poll = new PollEntity();
        $this->orm->polls->attach($poll);
        foreach ($data as $key => $value) {
            $poll->$key = $value;
        }
        $poll->author = $this->user->id;
        $this->orm->polls->persistAndFlush($poll);
    }

    /**
     * Check whether specified poll exists
     */
    public function exists(int $id): bool
    {
        return $this->orm->polls->getById($id) !== null;
    }

    /**
     * Edit specified poll
     *
     * @throws AuthenticationNeededException
     * @throws MissingPermissionsException
     * @throws PollNotFoundException
     */
    public function edit(int $id, array $data): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException("This action requires authentication.");
        }
        if (!$this->user->isAllowed("poll", "add")) {
            throw new MissingPermissionsException("You don't have permissions for editing polls.");
        }
        $poll = $this->orm->polls->getById($id);
        if ($poll === null) {
            throw new PollNotFoundException("Specified poll does not exist.");
        }
        foreach ($data as $key => $value) {
            $poll->$key = $value;
        }
        $this->orm->polls->persistAndFlush($poll);
    }
}
