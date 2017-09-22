<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class TownsMarketControlTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  use \Testbench\TComponent;
  
  /** @var TownsMarketControl */
  protected $control;
  
  public function setUp() {
    static $control = NULL;
    if(is_null($control)) {
      $control = $this->getService(ITownsMarketControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  function testRender() {
    $this->login();
    $this->checkRenderOutput($this->control, __DIR__ . "/townsMarketExpected.latte");
  }
}

$test = new TownsMarketControlTest();
$test->run();
?>