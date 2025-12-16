<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class UserProfileLinkControlTest extends \Tester\TestCase
{
    use \Nexendrie\Model\TUserControl;
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
        $this->login();
        $this->checkRenderOutput($this->control, __DIR__ . "/userProfileLinkExpected.latte", [$this->getUser()]);
    }

    public function testRenderWithTitle(): void
    {
        $this->login();
        $this->checkRenderOutput($this->control, __DIR__ . "/userProfileLinkWithTitleExpected.latte", [$this->getUser(), true]);
    }
}

$test = new UserProfileLinkControlTest();
$test->run();
