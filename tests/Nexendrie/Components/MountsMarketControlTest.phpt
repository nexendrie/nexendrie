<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

class MountsMarketControlTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  use \Testbench\TComponent;
  
  /** @var MountsMarketControl */
  protected $control;
  
  public function setUp() {
    static $control = NULL;
    if(is_null($control)) {
      $control = $this->getService(IMountsMarketControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  function testRender() {
    $this->login();
    $this->checkRenderOutput($this->control, __DIR__ . "/mountsMarketExpected.latte");
  }
}

$test = new MountsMarketControlTest;
$test->run();
?>