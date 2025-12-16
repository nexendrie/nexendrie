<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class TownsMarketControlTest extends \Tester\TestCase
{
    use \Nexendrie\Model\TUserControl;
    use \Testbench\TComponent;

    protected TownsMarketControl $control;

    protected function setUp(): void
    {
        static $control = null;
        if (is_null($control)) {
            /** @var TownsMarketControlFactory $factory */
            $factory = $this->getService(TownsMarketControlFactory::class);
            $control = $factory->create();
        }
        $this->control = $control;
        $this->attachToPresenter($this->control);
    }

    public function testRender(): void
    {
        $this->login();
        $this->checkRenderOutput($this->control, __DIR__ . "/townsMarketExpected.latte");
    }
}

$test = new TownsMarketControlTest();
$test->run();
