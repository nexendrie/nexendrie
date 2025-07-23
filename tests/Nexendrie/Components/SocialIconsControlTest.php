<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class SocialIconsControlTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \Testbench\TComponent;

  protected SocialIconsControl $control;
  
  protected function setUp(): void {
    static $control = null;
    if($control === null) {
      /** @var ISocialIconsControlFactory $factory */
      $factory = $this->getService(ISocialIconsControlFactory::class);
      $control = $factory->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testRender(): void {
    $this->checkRenderOutput($this->control, __DIR__ . "/socialIconsExpected.latte", ["https://www.nexendrie.cz/"]);
  }
}

$test = new SocialIconsControlTest();
$test->run();
?>