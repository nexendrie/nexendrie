<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Tester\Assert,
    Nexendrie\Model\TUserControl;

require __DIR__ . "/../../bootstrap.php";

class ConditionPathTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var ConditionPath */
  protected $condition;
  
  public function setUp() {
    $this->condition = $this->getService(ConditionPath::class);
  }
  
  public function testGetName() {
    Assert::type("string", $this->condition->getName());
  }
  
  public function testIsAllowed() {
    Assert::false($this->condition->isAllowed());
    $this->login("jakub");
    Assert::false($this->condition->isAllowed("site:manage"));
    Assert::exception(function() {
      $this->condition->isAllowed();
    }, \InvalidArgumentException::class);
  }
}

$test = new ConditionPathTest;
$test->run();
?>