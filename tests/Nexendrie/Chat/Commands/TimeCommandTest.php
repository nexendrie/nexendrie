<?php
declare(strict_types=1);

namespace Nexendrie\Chat\Commands;

require __DIR__ . "/../../../bootstrap.php";

use Tester\Assert;

final class TimeCommandTest extends \Tester\TestCase {
  protected TimeCommand $command;
  
  use \Testbench\TCompiledContainer;
  
  public function setUp(): void {
    $this->command = $this->getService(TimeCommand::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testGetName(): void {
    Assert::same("time", $this->command->name);
  }
  
  public function testExecute(): void {
    $time = $this->command->execute();
    Assert::contains("Aktuální čas je ", $time);
    Assert::contains(date("j.n.Y "), $time);
  }
}

$test = new TimeCommandTest();
$test->run();
?>