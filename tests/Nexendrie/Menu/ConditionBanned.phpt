<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Tester\Assert,
    Nexendrie\Model\TUserControl;

require __DIR__ . "/../../bootstrap.php";

class ConditionBannedTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var ConditionBanned */
  protected $condition;
  
  function setUp() {
    $this->condition = $this->getService(ConditionBanned::class);
  }
  
  function testGetName() {
    Assert::type("string", $this->condition->getName());
  }
  
  function testIsAllowed() {
    Assert::false($this->condition->isAllowed());
    $this->login();
    Assert::false($this->condition->isAllowed(true));
    Assert::true($this->condition->isAllowed(false));
    Assert::exception(function() {
      $this->condition->isAllowed();
    }, \InvalidArgumentException::class);
  }
}

$test = new ConditionBannedTest;
$test->run();
?>