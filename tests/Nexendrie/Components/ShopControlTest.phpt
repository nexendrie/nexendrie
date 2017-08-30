<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Tester\Assert,
    Nexendrie\Model\ShopNotFoundException;

require __DIR__ . "/../../bootstrap.php";

class ShopControlTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  use \Testbench\TComponent;
  
  /** @var ShopControl */
  protected $control;
  
  public function setUp() {
    static $control = NULL;
    if(is_null($control)) {
      $control = $this->getService(IShopControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testInvalidShop() {
    Assert::exception(function() {
      $this->control->id = 50;
    }, ShopNotFoundException::class);
  }
  
  public function testRender() {
    $this->control->id = 1;
    $this->checkRenderOutput($this->control, __DIR__ . "/shopExpected.latte");
  }
  
  public function testRenderUserLoggedIn() {
    $this->login();
    $this->control->id = 1;
    $this->checkRenderOutput($this->control, __DIR__ . "/shopUserExpected.latte");
  }
}

$test = new ShopControlTest;
$test->run();
?>