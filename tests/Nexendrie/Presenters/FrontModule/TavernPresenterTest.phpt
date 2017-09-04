<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

class TavernPresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Tavern:default");
    $this->login();
    $this->checkAction(":Front:Tavern:default");
  }
}

$test = new TavernPresenterTest;
$test->run();
?>