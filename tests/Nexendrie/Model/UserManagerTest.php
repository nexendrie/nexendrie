<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\User as UserEntity;

require __DIR__ . "/../../bootstrap.php";

final class UserManagerTest extends \Tester\TestCase
{
    use TUserControl;

    protected UserManager $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(UserManager::class); // @phpstan-ignore assign.propertyType
    }

    public function testNameAvailable(): void
    {
        Assert::true($this->model->nameAvailable("abc"));
        Assert::false($this->model->nameAvailable("Vladěna"));
        Assert::false($this->model->nameAvailable("Vladěna", 1));
    }

    public function testEmailAvailable(): void
    {
        Assert::true($this->model->emailAvailable("abc"));
        Assert::false($this->model->emailAvailable("admin@localhost"));
        Assert::true($this->model->emailAvailable("admin@localhost", 0));
        Assert::false($this->model->emailAvailable("admin@localhost", 1));
    }

    public function testRegister(): void
    {
        /** @var \Nexendrie\Orm\Model $orm */
        $orm = $this->getService(\Nexendrie\Orm\Model::class);
        /** @var UserEntity $user */
        $user = $orm->users->getById(1);
        Assert::exception(function () use ($user) {
            $this->model->register(["publicname" => $user->publicname, "email" => "abc"]);
        }, RegistrationException::class, null, UserManager::REG_DUPLICATE_NAME);
        Assert::exception(function () use ($user) {
            $this->model->register(["email" => $user->email, "publicname" => "abc"]);
        }, RegistrationException::class, null, UserManager::REG_DUPLICATE_EMAIL);
        $data = [
            "publicname" => "abc", "email" => "abc", "password" => "abcd",
        ];
        $this->model->register($data);
        /** @var UserEntity $user */
        $user = $orm->users->getByPublicname($data["publicname"]);
        Assert::type(UserEntity::class, $user);
        Assert::notSame($data["password"], $user->password);
        $orm->users->removeAndFlush($user);
    }

    public function testGetSettings(): void
    {
        Assert::exception(function () {
            $this->model->getSettings();
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::type("array", $this->model->getSettings());
    }

    public function testChangeSettings(): void
    {
        Assert::exception(function () {
            $this->model->changeSettings([]);
        }, AuthenticationNeededException::class);
        $this->login();
        $user = $this->getUser();
        /** @var \Nexendrie\Orm\Model $orm */
        $orm = $this->getService(\Nexendrie\Orm\Model::class);
        /** @var UserEntity $user2 */
        $user2 = $orm->users->getById(0);
        Assert::exception(function () use ($user2) {
            $this->model->changeSettings(["publicname" => $user2->publicname]);
        }, SettingsException::class, null, UserManager::REG_DUPLICATE_NAME);
        Assert::exception(function () use ($user, $user2) {
            $this->model->changeSettings([
                "email" => $user2->email, "publicname" => $user->publicname
            ]);
        }, SettingsException::class, null, UserManager::REG_DUPLICATE_EMAIL);
        Assert::exception(function () use ($user) {
            $this->model->changeSettings([
                "email" => $user->email, "publicname" => $user->publicname, "password_old" => "abc",
                "password_new" => "abc"
            ]);
        }, SettingsException::class, null, UserManager::SET_INVALID_PASSWORD);
        $this->preserveStats(["password", "money"], function () use ($user) {
            $password = $user->password;
            $this->model->changeSettings([
                "email" => $user->email, "publicname" => $user->publicname, "password_old" => "qwerty",
                "password_new" => "abc", "money" => 1
            ]);
            Assert::notSame($password, $user->password);
            Assert::notSame("abc", $user->password);
            Assert::same(1, $user->money);
        });
    }

    public function testListOfUsers(): void
    {
        $result = $this->model->listOfUsers();
        Assert::type(ICollection::class, $result);
        Assert::type(UserEntity::class, $result->fetch());
    }

    public function testEdit(): void
    {
        Assert::exception(function () {
            $this->model->edit(50, []);
        }, UserNotFoundException::class);
        $user = $this->model->get(1);
        $money = $user->money;
        $this->model->edit(1, ["money" => 1]);
        Assert::same(1, $user->money);
        $this->model->edit(1, ["money" => $money]);
    }

    public function testGet(): void
    {
        $user = $this->model->get(0);
        Assert::type(UserEntity::class, $user);
        Assert::exception(function () {
            $this->model->get(50);
        }, UserNotFoundException::class);
    }
}

$test = new UserManagerTest();
$test->run();
