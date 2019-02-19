<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Tester\Assert;
use Nexendrie\Model\TUserControl;

require __DIR__ . "/../../bootstrap.php";

final class ConditionPathTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var ConditionPath */
  protected $condition;
  
  protected function setUp() {
    $this->condition = $this->getService(ConditionPath::class);
  }
  
  public function testGetName() {
    Assert::same("path", $this->condition->getName());
  }
  
  public function testIsAllowed() {
    Assert::false($this->condition->isAllowed());
    $this->login("Jakub");
    Assert::false($this->condition->isAllowed("site:manage"));
    Assert::exception(function() {
      $this->condition->isAllowed();
    }, \InvalidArgumentException::class);
  }
}

$test = new ConditionPathTest();
$test->run();
?>