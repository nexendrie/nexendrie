<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nette\InvalidArgumentException,
    Nette\Security\AuthenticationException,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\User as UserEntity,
    Nette\Security\User;

require __DIR__ . "/../../bootstrap.php";

final class UserManagerTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var UserManager */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(UserManager::class);
  }
  
  public function testNameAvailable() {
    Assert::true($this->model->nameAvailable("abc"));
    Assert::true($this->model->nameAvailable("abc", "publicname"));
    Assert::exception(function() {
      $this->model->nameAvailable("abc", "abc");
    }, InvalidArgumentException::class);
    Assert::exception(function() {
      $this->model->nameAvailable("abc", "abc", 50);
    }, InvalidArgumentException::class);
    Assert::false($this->model->nameAvailable("system"));
    Assert::true($this->model->nameAvailable("system", "username", 0));
    Assert::false($this->model->nameAvailable("system", "username", 1));
    Assert::false($this->model->nameAvailable("Vladěna", "publicname"));
    Assert::true($this->model->nameAvailable("system", "publicname", 0));
    Assert::false($this->model->nameAvailable("Vladěna", "publicname", 1));
  }
  
  public function testEmailAvailable() {
    Assert::true($this->model->emailAvailable("abc"));
    Assert::false($this->model->emailAvailable("admin@localhost"));
    Assert::true($this->model->emailAvailable("admin@localhost", 0));
    Assert::false($this->model->emailAvailable("admin@localhost", 1));
  }
  
  public function testAuthenticate() {
    $user = "admin";
    $password = "qwerty";
    $identity = $this->model->authenticate([$user, $password]);
    Assert::type(\Nette\Security\Identity::class, $identity);
    Assert::same(1, $identity->id);
    Assert::exception(function() use($user) {
      $this->model->authenticate([$user, "abc"]);
    }, AuthenticationException::class);
    Assert::exception(function() {
      $this->model->authenticate(["abc", "abc"]);
    }, AuthenticationException::class);
  }
  
  public function testRefreshIdentity() {
    /** @var User $user */
    $user = $this->getService(User::class);
    $this->model->user = $user;
    Assert::exception(function() {
      $this->model->refreshIdentity();
    }, AuthenticationNeededException::class);
    $this->login();
    $this->model->refreshIdentity();
  }
  
  public function testRegister() {
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $user = $orm->users->getById(1);
    Assert::exception(function() use($user) {
      $this->model->register(["username" => $user->username]);
    }, RegistrationException::class, NULL, UserManager::REG_DUPLICATE_USERNAME);
    Assert::exception(function() use($user) {
      $this->model->register(["email" => $user->email, "username" => "abc"]);
    }, RegistrationException::class, NULL, UserManager::REG_DUPLICATE_EMAIL);
  }
  
  public function testGetSettings() {
    Assert::exception(function() {
      $this->model->getSettings();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("array", $this->model->getSettings());
  }
  
  public function testListOfUsers() {
    $result = $this->model->listOfUsers();
    Assert::type(ICollection::class, $result);
    Assert::type(UserEntity::class, $result->fetch());
  }
  
  public function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, UserNotFoundException::class);
    $user = $this->model->get(1);
    $money = $user->money;
    $this->model->edit(1, ["money" => 1]);
    Assert::same(1, $user->money);
    $this->model->edit(1, ["money" => $money]);
  }
  
  public function testGet() {
    $user = $this->model->get(0);
    Assert::type(UserEntity::class, $user);
    Assert::exception(function() {
      $this->model->get(50);
    }, UserNotFoundException::class);
  }
}

$test = new UserManagerTest;
$test->run();
?>