<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\SettingsRepository;
use Nexendrie\Orm\Model as ORM;

/**
 * MonasteriesStatusTask
 *
 * @author Jakub Konečný
 */
final class MonasteriesStatusTask {
  private int $weeklyWearingOut;
  
  public function __construct(private readonly ORM $orm, SettingsRepository $sr) {
    $this->weeklyWearingOut = $sr->settings["buildings"]["weeklyWearingOut"];
  }
  
  /**
   * @cronner-task(Monasteries status update)
   * @cronner-period(1 week)
   * @cronner-time(00:00 - 01:00)
   */
  public function run(): void {
    echo "Starting monasteries status update ...\n";
    $monasteries = $this->orm->monasteries->findLedMonasteries();
    foreach($monasteries as $monastery) {
      $monastery->hp -= $this->weeklyWearingOut;
      $this->orm->monasteries->persist($monastery);
      echo "Decreasing (#$monastery->id) $monastery->name's life by $this->weeklyWearingOut.\n";
    }
    $this->orm->flush();
    echo "Finished monasteries status update ...\n";
  }
}
?>