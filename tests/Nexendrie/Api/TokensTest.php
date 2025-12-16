<?php
declare(strict_types=1);

namespace Nexendrie\Api;

require __DIR__ . "/../../bootstrap.php";

use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\TUserControl;
use Nexendrie\Orm\ApiToken;
use Tester\Assert;

final class TokensTest extends \Tester\TestCase
{
    use TUserControl;

    private Tokens $model;
    private \Nexendrie\Orm\Model $orm;

    protected function setUp(): void
    {
        $this->model = $this->getService(Tokens::class); // @phpstan-ignore assign.propertyType
        $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
    }

    public function testCreate(): void
    {
        Assert::exception(function () {
            $this->model->create();
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->create();
        }, ApiNotEnabledException::class);
        $this->modifyUser(["api" => true,], function () {
            $token = $this->model->create();
            Assert::type(ApiToken::class, $token);
            Assert::true(strlen($token->token) === $this->model->length);
            Assert::true($token->created <= time());
            Assert::true($token->expire > time());
        });
    }

    public function testInvalidate(): void
    {
        Assert::exception(function () {
            $this->model->invalidate("abc");
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->invalidate("abc");
        }, TokenNotFoundException::class);
        $token = null;
        $this->modifyUser(["api" => true,], function () use (&$token) {
            $token = $this->model->create()->token;
        });
        /** @var string $token */
        Assert::noError(function () use ($token) {
            $this->model->invalidate($token);
        });
        Assert::exception(function () use ($token) {
            $this->model->invalidate($token);
        }, TokenExpiredException::class);
        $this->login("Rahym");
        Assert::exception(function () use ($token) {
            $this->model->invalidate($token);
        }, TokenNotFoundException::class);
    }
}

$test = new TokensTest();
$test->run();
