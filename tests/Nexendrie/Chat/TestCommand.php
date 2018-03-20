<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

final class TestCommand extends ChatCommand {
  protected $name = "test1";
  
  public function execute(): string {
    return "passed";
  }
}
?>