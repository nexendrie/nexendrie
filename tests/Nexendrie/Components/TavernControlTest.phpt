<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class TavernControlTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  use \Testbench\TComponent;
  
  /** @var TavernControl */
  protected $control;
  
  protected function setUp() {
    static $control = null;
    if(is_null($control)) {
      $control = $this->getService(ITavernControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testRender() {
    $this->checkRenderOutput($this->control, __DIR__ . "/tavernExpected.latte");
  }
  
  public function testRenderUserLoggedIn() {
    $this->login();
    $this->checkRenderOutput($this->control, __DIR__ . "/tavernUserExpected.latte");
  }
}

$test = new TavernControlTest();
$test->run();
?>