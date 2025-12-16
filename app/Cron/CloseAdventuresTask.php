<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\UserAdventure;

/**
 * CloseAdventureTask
 *
 * @author Jakub Konečný
 */
final class CloseAdventuresTask
{
    public function __construct(private readonly ORM $orm)
    {
    }

    /**
     * @cronner-task(Close adventures)
     * @cronner-period(1 day)
     * @cronner-time(00:00 - 01:00)
     */
    public function run(): void
    {
        echo "Starting closing adventures ...\n";
        $adventures = $this->orm->userAdventures->findOpenAdventures();
        foreach ($adventures as $adventure) {
            $adventure->progress = UserAdventure::PROGRESS_CLOSED;
            $this->orm->userAdventures->persistAndFlush($adventure);
        }
        echo "Finished closing adventures ...\n";
    }
}
