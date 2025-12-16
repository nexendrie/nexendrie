<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

/**
 * @skip
 */
final class MountsMarketControlTest extends \Tester\TestCase
{
    use \Nexendrie\Model\TUserControl;
    use \Testbench\TComponent;

    protected MountsMarketControl $control;

    protected function setUp(): void
    {
        static $control = null;
        if ($control === null) {
            /** @var MountsMarketControlFactory $factory */
            $factory = $this->getService(MountsMarketControlFactory::class);
            $control = $factory->create();
        }
        $this->control = $control;
        $this->attachToPresenter($this->control);
    }

    public function testRender(): void
    {
        $this->login();
        $this->checkRenderOutput($this->control, __DIR__ . "/mountsMarketExpected.latte");
    }
}

$test = new MountsMarketControlTest();
$test->run();
