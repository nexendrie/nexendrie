<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Tester\Assert;
use Nexendrie\Model\TUserControl;

require __DIR__ . "/../../bootstrap.php";

final class ConditionBannedTest extends \Tester\TestCase {
  use TUserControl;

  protected ConditionBanned $condition;
  
  protected function setUp(): void {
    $this->condition = $this->getService(ConditionBanned::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testGetName(): void {
    Assert::same("banned", $this->condition->getName());
  }
  
  public function testIsAllowed(): void {
    Assert::false($this->condition->isAllowed());
    $this->login();
    Assert::false($this->condition->isAllowed(true));
    Assert::true($this->condition->isAllowed(false));
    Assert::exception(function() {
      $this->condition->isAllowed();
    }, \InvalidArgumentException::class);
  }
}

$test = new ConditionBannedTest();
$test->run();
?>