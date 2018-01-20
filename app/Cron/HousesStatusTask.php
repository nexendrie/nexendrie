<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * HousesStatusTask
 *
 * @author Jakub Konečný
 */
class HousesStatusTask {
  use \Nette\SmartObject;
  
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @cronner-task Houses status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  public function run(): void {
    echo "Starting houses status update ...\n";
    $houses = $this->orm->houses->findOwnedHouses();
    foreach($houses as $house) {
      $house->hp -= 3;
      $this->orm->houses->persist($house);
      echo "Decreasing house (#$house->id)'s life by 3.\n";
    }
    $this->orm->flush();
    echo "Finished houses status update ...\n";
  }
}
?>