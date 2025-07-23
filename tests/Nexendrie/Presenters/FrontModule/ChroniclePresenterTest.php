<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class ChroniclePresenterTest extends \Tester\TestCase {
  use \Nexendrie\Presenters\TPresenter;
  
  public function testDefault(): void {
    $this->checkAction(":Front:Chronicle:default");
  }
  
  public function testCrimes(): void {
    $this->checkAction(":Front:Chronicle:crimes");
  }
  
  public function testMarriages(): void {
    $this->checkAction(":Front:Chronicle:marriages");
  }
  
  public function testEvents(): void {
    $this->checkAction(":Front:Chronicle:events");
  }
}

$test = new ChroniclePresenterTest();
$test->run();
?>