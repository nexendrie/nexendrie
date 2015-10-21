<?php
namespace Nexendrie;

/**
 * Cron Tasks
 *
 * @author Jakub Konečný
 */
class CronTasks {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Mounts status update
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Mounts status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  function mountsStatus() {
    echo "Starting mounts status update ...\n";
    $mounts = $this->orm->mounts->findOwnedMounts();
    foreach($mounts as $mount) {
      $mount->hp -= 5;
      $this->orm->mounts->persist($mount);
      echo "Decreasing (#$mount->id) $mount->name's life by 5.\n";
    }
    $this->orm->flush();
    echo "Finished mounts status update ...\n";
  }
}
?>