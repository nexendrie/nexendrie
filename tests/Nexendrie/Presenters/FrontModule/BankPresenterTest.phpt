<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class BankPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Bank:default");
    $this->login();
    $this->checkAction(":Front:Bank:default");
  }
  
  public function testReturn() {
    $this->checkRedirect(":Front:Bank:return", "/user/login");
  }
}

$test = new BankPresenterTest;
$test->run();
?>