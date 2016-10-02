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

class UserManagerTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \TUserControl;
  
  /** @var UserManager */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(UserManager::class);
  }
  
  function testNameAvailable() {
    Assert::true($this->model->nameAvailable("abc"));
    Assert::true($this->model->nameAvailable("abc", "publicname"));
    Assert::exception(function() {
      $this->model->nameAvailable("abc", "abc");
    }, InvalidArgumentException::class);
    Assert::exception(function() {
      $this->model->nameAvailable("abc", "abc", "abc");
    }, InvalidArgumentException::class);
    Assert::false($this->model->nameAvailable("system"));
    Assert::true($this->model->nameAvailable("system", "username", 0));
    Assert::false($this->model->nameAvailable("system", "username", 1));
    Assert::false($this->model->nameAvailable("Vladěna", "publicname"));
    Assert::true($this->model->nameAvailable("system", "publicname", 0));
    Assert::false($this->model->nameAvailable("Vladěna", "publicname", 1));
  }
  
  function testEmailAvailable() {
    Assert::true($this->model->emailAvailable("abc"));
    Assert::false($this->model->emailAvailable("admin@localhost"));
    Assert::true($this->model->emailAvailable("admin@localhost", 0));
    Assert::false($this->model->emailAvailable("admin@localhost", 1));
    Assert::exception(function() {
      $this->model->emailAvailable("abc", "abc");
    }, InvalidArgumentException::class);
  }
  
  function testAuthenticate() {
    $user = getenv("APP_USER");
    $password = getenv("APP_PASSWORD");
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
  
  function testRefreshIdentity() {
    /** @var User $user */
    $user = $this->getService(User::class);
    $this->model->user = $user;
    Assert::exception(function() {
      $this->model->refreshIdentity();
    }, AuthenticationNeededException::class);
    $this->login();
    $this->model->refreshIdentity();
  }
  
  function testGetSettings() {
    Assert::exception(function() {
      $this->model->getSettings();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type("array", $this->model->getSettings());
  }
  
  function testListOfUsers() {
    $result = $this->model->listOfUsers();
    Assert::type(ICollection::class, $result);
    Assert::type(UserEntity::class, $result->fetch());
  }
  
  function testGet() {
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