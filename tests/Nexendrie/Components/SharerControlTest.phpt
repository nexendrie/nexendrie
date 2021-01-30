<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class SharerControlTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \Testbench\TComponent;
  
  /** @var SharerControl */
  protected $control;
  
  protected function setUp() {
    static $control = null;
    if($control === null) {
      $control = $this->getService(ISharerControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testRender() {
    $this->checkRenderOutput($this->control, __DIR__ . "/sharerExpected.latte", ["https://www.nexendrie.cz/", "článek"]);
  }
}

$test = new SharerControlTest();
$test->run();
?>