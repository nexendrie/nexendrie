<?php
declare(strict_types=1);

namespace Nexendrie\Chat\Commands;

use HeroesofAbenez\Chat\BaseChatCommand;

/**
 * Chat Command Time
 *
 * @author Jakub Konečný
 */
class TimeCommand extends BaseChatCommand
{
    public function execute(): string
    {
        $time = date("j.n.Y G:i:s");
        return "Aktuální čas je $time.";
    }
}
