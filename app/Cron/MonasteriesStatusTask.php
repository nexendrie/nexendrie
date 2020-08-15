<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\SettingsRepository;

/**
 * MonasteriesStatusTask
 *
 * @author Jakub Konečný
 */
final class MonasteriesStatusTask {
  use \Nette\SmartObject;

  protected \Nexendrie\Orm\Model $orm;
  protected int $weeklyWearingOut;
  
  public function __construct(\Nexendrie\Orm\Model $orm, SettingsRepository $sr) {
    $this->orm = $orm;
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