<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class HotReloadingControlTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;
    use \Testbench\TComponent;

    protected UserProfileLinkControl $control;

    protected function setUp(): void
    {
        static $control = null;
        if (is_null($control)) {
            /** @var UserProfileLinkControlFactory $factory */
            $factory = $this->getService(UserProfileLinkControlFactory::class);
            $control = $factory->create();
        }
        $this->control = $control;
        $this->attachToPresenter($this->control);
    }

    public function testRender(): void
    {
        /** @var HotReloadingControlFactory $factory */
        $factory = $this->getService(HotReloadingControlFactory::class);
        $control = $factory->create();

        $control->url = null;
        $this->checkRenderOutput($control, "");

        $control->url = "https://nexendrie.localhost/.well-known/mercure?topic=https://frankenphp.dev/hot-reload/xxx";
    }
}

$test = new HotReloadingControlTest();
$test->run();
