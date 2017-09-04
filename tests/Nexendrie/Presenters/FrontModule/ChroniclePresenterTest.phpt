<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

class ChroniclePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault() {
    $this->checkAction(":Front:Chronicle:default");
  }
  
  public function testCrimes() {
    $this->checkAction(":Front:Chronicle:crimes");
  }
  
  public function testMarriages() {
    $this->checkAction(":Front:Chronicle:marriages");
  }
  
  public function testEvents() {
    $this->checkAction(":Front:Chronicle:events");
  }
}

$test = new ChroniclePresenterTest;
$test->run();
?>