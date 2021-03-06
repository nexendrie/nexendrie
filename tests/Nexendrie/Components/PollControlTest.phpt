<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\PollNotFoundException;
use Tester\Assert;
use Nexendrie\Orm\Poll;

require __DIR__ . "/../../bootstrap.php";

final class PollControlTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  use \Testbench\TComponent;
  
  /** @var PollControl */
  protected $control;
  
  protected function setUp() {
    static $control = null;
    if(is_null($control)) {
      $control = $this->getService(IPollControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testInvalidId() {
    Assert::exception(function() {
      $this->control->id = 50;
    }, PollNotFoundException::class);
  }
  
  public function testGetPoll() {
    $this->control->id = 1;
    Assert::type(Poll::class, $this->control->getPoll());
  }
  
  public function testRender() {
    $this->control->id = 1;
    $this->checkRenderOutput($this->control, __DIR__ . "/pollExpected.latte");
  }
  
  public function testRenderUserLoggedIn() {
    $this->login("Jakub");
    $this->control->id = 1;
    $this->checkRenderOutput($this->control, __DIR__ . "/pollUserExpected.latte");
  }
  
  public function testRenderAdminLoggedIn() {
    $this->login();
    $this->control->id = 1;
    $this->checkRenderOutput($this->control, __DIR__ . "/pollAdminExpected.latte");
  }
}

$test = new PollControlTest();
$test->run();
?>