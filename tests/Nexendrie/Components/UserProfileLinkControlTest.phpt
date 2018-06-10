<?php
declare(strict_types=1);

namespace Nexendrie\Components;

require __DIR__ . "/../../bootstrap.php";

final class UserProfileLinkControlTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  use \Testbench\TComponent;
  
  /** @var UserProfileLinkControl */
  protected $control;
  
  protected function setUp() {
    static $control = null;
    if(is_null($control)) {
      $control = $this->getService(IUserProfileLinkControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testRender() {
    $this->login();
    $this->checkRenderOutput($this->control, __DIR__ . "/userProfileLinkExpected.latte", [$this->getUser()]);
  }
  
  public function testRenderWithTitle() {
    $this->login();
    $this->checkRenderOutput($this->control, __DIR__ . "/userProfileLinkWithTitleExpected.latte", [$this->getUser(), true]);
  }
}

$test = new UserProfileLinkControlTest();
$test->run();
?>