<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\SettingsRepository;

/**
 * HousesStatusTask
 *
 * @author Jakub Konečný
 */
final class HousesStatusTask {
  use \Nette\SmartObject;

  protected \Nexendrie\Orm\Model $orm;
  protected int $weeklyWearingOut;
  
  public function __construct(\Nexendrie\Orm\Model $orm, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->weeklyWearingOut = $sr->settings["buildings"]["weeklyWearingOut"];
  }
  
  /**
   * @cronner-task Houses status update
   * @cronner-period 1 week
   * @cronner-time 00:00 - 01:00
   */
  public function run(): void {
    echo "Starting houses status update ...\n";
    $houses = $this->orm->houses->findOwnedHouses();
    foreach($houses as $house) {
      $house->hp -= $this->weeklyWearingOut;
      $this->orm->houses->persist($house);
      echo "Decreasing house (#$house->id)'s life by $this->weeklyWearingOut.\n";
    }
    $this->orm->flush();
    echo "Finished houses status update ...\n";
  }
}
?>