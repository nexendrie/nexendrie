<?php
declare(strict_types=1);

namespace Nexendrie\Chat\Commands;

use HeroesofAbenez\Chat\ChatCommand;

/**
 * Chat Command Time
 *
 * @author Jakub Konečný
 */
class TimeCommand extends ChatCommand {
  public function execute(): string {
    $time = date("j.n.Y G:i:s");
    return "Aktuální čas je $time.";
  }
}
?>