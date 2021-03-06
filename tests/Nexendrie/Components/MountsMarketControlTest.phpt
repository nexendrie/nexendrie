<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

/**
 * @skip
 */
final class MountsMarketControlTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  use \Testbench\TComponent;
  
  /** @var MountsMarketControl */
  protected $control;
  
  protected function setUp() {
    static $control = null;
    if(is_null($control)) {
      $control = $this->getService(IMountsMarketControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testRender() {
    $this->login();
    $this->checkRenderOutput($this->control, __DIR__ . "/mountsMarketExpected.latte");
  }
}

$test = new MountsMarketControlTest();
$test->run();
?>