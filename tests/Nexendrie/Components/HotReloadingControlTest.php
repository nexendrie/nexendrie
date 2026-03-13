<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class HotReloadingControlTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;
    use \Testbench\TComponent;

    public function testRender(): void
    {
        /** @var HotReloadingControlFactory $factory */
        $factory = $this->getService(HotReloadingControlFactory::class);
        $control = $factory->create();

        $control->url = null;
        $this->checkRenderOutput($control, "");

        $control->url = "https://nexendrie.localhost/.well-known/mercure?topic=https://frankenphp.dev/hot-reload/xxx";
        $this->checkRenderOutput($control, __DIR__ . "/hotReloadingExpected.latte");
    }
}

$test = new HotReloadingControlTest();
$test->run();
