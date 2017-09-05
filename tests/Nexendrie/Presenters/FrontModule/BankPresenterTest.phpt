<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

class BankPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Bank:default");
    $this->login();
    $this->checkAction(":Front:Bank:default");
  }
}

$test = new BankPresenterTest;
$test->run();
?>