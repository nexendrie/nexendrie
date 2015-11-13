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
  /** @var \Nexendrie\Model\Taxes */
  protected $taxesModel;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\Taxes $taxesModel) {
    $this->orm = $orm;
    $this->taxesModel = $taxesModel;
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
  
  /**
   * Taxes
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Taxes
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  function taxes() {
    $date = new \DateTime;
    $date->setTimestamp(time());
    if($date->format("j") != 1) return;
    echo "Starting paying taxes ...\n";
    $result = $this->taxesModel->payTaxes();
    foreach($result as $town) {
      echo "Town (#$town->id) $town->name ...\n";
      foreach($town->denizens as $denizen) {
        echo "$denizen->publicname ";
        if($town->owner === $denizen->id) {
          echo "owns the town. He/she is not paying taxes.\n";
          continue;
        }
        echo "earned $denizen->income and will pay $denizen->tax to his/her liege.\n";
      }
    }
    echo "Finished paying taxes ...\n";
  }
  
  /**
   * Close adventures
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Close adventures
   * @cronner-period 1 day
   * @cronner-time 00:00 - 01:00
   */
  function closeAdventures() {
    echo "Starting closing adventures ...\n";
    $adventures = $this->orm->userAdventures->findOpenAdventures();
    foreach($adventures as $adventure) {
      $adventure->progress = 10;
      $this->orm->userAdventures->persistAndFlush($adventure);
    }
    echo "Finished closing adventures ...\n";
  }
  
  /**
   * Monasteries status update
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Monasteries status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  function monasteriesStatus() {
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