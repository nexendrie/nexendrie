<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * CastlesStatusTask
 *
 * @author Jakub Konečný
 */
class CastlesStatusTask {
  use \Nette\SmartObject;
  
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @cronner-task Castles status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  public function run(): void {
    echo "Starting castles status update ...\n";
    $castles = $this->orm->castles->findOwnedCastles();
    foreach($castles as $castle) {
      $castle->hp -= 3;
      $this->orm->castles->persist($castle);
      echo "Decreasing (#$castle->id) $castle->name's life by 3.\n";
    }
    $this->orm->flush();
    echo "Finished castles status update ...\n";
  }
}
?>