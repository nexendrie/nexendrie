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
  /** @var \Nexendrie\Model\Job */
  protected $jobModel;
  /** @var int */
  protected $taxRate;
  
  function __construct($taxRate, \Nexendrie\Orm\Model $orm, \Nexendrie\Model\Job $jobModel) {
    $this->orm = $orm;
    $this->jobModel = $jobModel;
    $this->taxRate = $taxRate;
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
    $date->modify("-1 day");
    $month = $date->format("n");
    $year = $date->format("Y");
    echo "Starting paying taxes ...\n";
    $towns = $this->orm->towns->findAll();
    /* @var $town \Nexendrie\Orm\Town */
    foreach($towns as $town) {
      echo "Town (#$town->id) $town->name ...\n";
      $taxes = 0;
      /* @var $denizen \Nexendrie\Orm\User */
      foreach($town->denizens as $denizen) {
        if($denizen->id === 0) continue;
        echo "$denizen->publicname ";
        if($town->owner->id === $denizen->id) {
          echo "owns the town. He/she is not paying taxes.\n";
          continue;
        }
        $income = $this->jobModel->calculateMonthJobIncome($denizen->id, $month, $year);
        $tax = (int) round(@($income / 100 * $this->taxRate));
        echo "earned $income and will pay $tax.\n";
        $taxes += $tax;
        if($tax > 0) $denizen->money -= $tax;
      }
      if($taxes > 0) {
        $town->owner->money += $taxes;
        $this->orm->towns->persistAndFlush($town);
      }
    }
    echo "Finished paying taxes ...\n";
  }
}
?>