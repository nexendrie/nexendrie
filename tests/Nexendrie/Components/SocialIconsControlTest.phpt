<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class SocialIconsControlTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \Testbench\TComponent;
  
  /** @var SocialIconsControl */
  protected $control;
  
  protected function setUp() {
    static $control = null;
    if($control === null) {
      $control = $this->getService(ISocialIconsControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testRender() {
    $this->checkRenderOutput($this->control, __DIR__ . "/socialIconsExpected.latte", ["https://www.nexendrie.cz/"]);
  }
}

$test = new SocialIconsControlTest();
$test->run();
?>