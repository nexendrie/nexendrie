<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\SettingsRepository;
use Nexendrie\Orm\Model as ORM;

/**
 * CastlesStatusTask
 *
 * @author Jakub Konečný
 */
final class CastlesStatusTask
{
    private int $weeklyWearingOut;

    public function __construct(private readonly ORM $orm, SettingsRepository $sr)
    {
        $this->weeklyWearingOut = $sr->settings["buildings"]["weeklyWearingOut"];
    }

    /**
     * @cronner-task(Castles status update)
     * @cronner-period(1 week)
     * @cronner-time(00:00 - 01:00)
     */
    public function run(): void
    {
        echo "Starting castles status update ...\n";
        $castles = $this->orm->castles->findOwnedCastles();
        foreach ($castles as $castle) {
            $castle->hp -= $this->weeklyWearingOut;
            $this->orm->castles->persist($castle);
            echo "Decreasing (#$castle->id) $castle->name's life by $this->weeklyWearingOut.\n";
        }
        $this->orm->flush();
        echo "Finished castles status update ...\n";
    }
}
