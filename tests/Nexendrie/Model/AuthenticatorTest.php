<?php
declare(strict_types=1);

namespace Nexendrie\Model;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

final class AuthenticatorTest extends \Tester\TestCase {
  use TUserControl;

  protected Authenticator $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Authenticator::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testAuthenticate(): void {
    $user = "jakub.konecny2@centrum.cz";
    $password = "qwerty";
    $identity = $this->model->authenticate($user, $password);
    Assert::type(\Nette\Security\SimpleIdentity::class, $identity);
    Assert::same(1, $identity->id); // @phpstan-ignore property.deprecatedClass
    Assert::exception(function() use($user) {
      $this->model->authenticate($user, "abc");
    }, AuthenticationException::class);
    Assert::exception(function() {
      $this->model->authenticate("abc", "abc");
    }, AuthenticationException::class);
  }
  
  public function testRefreshIdentity(): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    $this->model->user = $user;
    Assert::exception(function() {
      $this->model->refreshIdentity();
    }, AuthenticationNeededException::class);
    $this->login();
    $this->model->refreshIdentity();
  }
}

$test = new AuthenticatorTest();
$test->run();
?>