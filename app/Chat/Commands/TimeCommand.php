<?php
declare(strict_types=1);

namespace Nexendrie\Chat\Commands;

/**
 * Chat Command Time
 *
 * @author Jakub Konečný
 */
class TimeCommand extends \Nexendrie\Chat\ChatCommand {
  public function execute(): string {
    $time = date("j.n.Y G:i:s");
    return "Aktuální čas je $time.";
  }
}
?>