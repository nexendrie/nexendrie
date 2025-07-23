<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Tester\Assert;
use Nexendrie\Model\TUserControl;

require __DIR__ . "/../../bootstrap.php";

final class ConditionPathTest extends \Tester\TestCase {
  use TUserControl;

  protected ConditionPath $condition;
  
  protected function setUp(): void {
    $this->condition = $this->getService(ConditionPath::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testGetName(): void {
    Assert::same("path", $this->condition->getName());
  }
  
  public function testIsAllowed(): void {
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