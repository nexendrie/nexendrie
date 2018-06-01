<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * MonasteriesStatusTask
 *
 * @author Jakub Konečný
 */
final class MonasteriesStatusTask {
  use \Nette\SmartObject;
  
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @cronner-task Monasteries status update
   * @cronner-period 1 week
   * @cronner-time 00:00 - 01:00
   */
  public function run(): void {
    echo "Starting monasteries status update ...\n";
    $monasteries = $this->orm->monasteries->findLedMonasteries();
    foreach($monasteries as $monastery) {
      $monastery->hp -= 3;
      $this->orm->monasteries->persist($monastery);
      echo "Decreasing (#$monastery->id) $monastery->name's life by 3.\n";
    }
    $this->orm->flush();
    echo "Finished monasteries status update ...\n";
  }
}
?>