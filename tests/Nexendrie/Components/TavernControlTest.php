<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class TavernControlTest extends \Tester\TestCase
{
    use \Nexendrie\Model\TUserControl;
    use \Testbench\TComponent;

    protected TavernControl $control;

    protected function setUp(): void
    {
        static $control = null;
        if (is_null($control)) {
            /** @var ITavernControlFactory $factory */
            $factory = $this->getService(ITavernControlFactory::class);
            $control = $factory->create();
        }
        $this->control = $control;
        $this->attachToPresenter($this->control);
    }

    public function testRender(): void
    {
        $this->checkRenderOutput($this->control, __DIR__ . "/tavernExpected.latte");
    }

    public function testRenderUserLoggedIn(): void
    {
        $this->login();
        $this->checkRenderOutput($this->control, __DIR__ . "/tavernUserExpected.latte");
    }
}

$test = new TavernControlTest();
$test->run();
