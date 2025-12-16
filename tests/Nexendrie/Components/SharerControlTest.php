<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class SharerControlTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;
    use \Testbench\TComponent;

    protected SharerControl $control;

    protected function setUp(): void
    {
        static $control = null;
        if ($control === null) {
            /** @var SharerControlFactory $factory */
            $factory = $this->getService(SharerControlFactory::class);
            $control = $factory->create();
        }
        $this->control = $control;
        $this->attachToPresenter($this->control);
    }

    public function testRender(): void
    {
        $this->checkRenderOutput(
            $this->control,
            __DIR__ . "/sharerExpected.latte",
            ["https://www.nexendrie.cz/", "Nexendrie hlavní stránka", "článek"]
        );
    }
}

$test = new SharerControlTest();
$test->run();
