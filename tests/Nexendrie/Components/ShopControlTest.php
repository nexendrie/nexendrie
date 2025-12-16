<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Tester\Assert;
use Nexendrie\Model\ShopNotFoundException;

require __DIR__ . "/../../bootstrap.php";

final class ShopControlTest extends \Tester\TestCase
{
    use \Nexendrie\Model\TUserControl;
    use \Testbench\TComponent;

    protected ShopControl $control;

    protected function setUp(): void
    {
        static $control = null;
        if (is_null($control)) {
            /** @var IShopControlFactory $factory */
            $factory = $this->getService(IShopControlFactory::class);
            $control = $factory->create();
        }
        $this->control = $control;
        $this->attachToPresenter($this->control);
    }

    public function testInvalidShop(): void
    {
        Assert::exception(function () {
            $this->control->id = 50;
        }, ShopNotFoundException::class);
    }

    public function testRender(): void
    {
        $this->control->id = 1;
        $this->checkRenderOutput($this->control, __DIR__ . "/shopExpected.latte");
    }

    public function testRenderUserLoggedIn(): void
    {
        $this->login();
        $this->control->id = 1;
        $this->checkRenderOutput($this->control, __DIR__ . "/shopUserExpected.latte");
    }
}

$test = new ShopControlTest();
$test->run();
