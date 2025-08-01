<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Mount as MountEntity;

/**
 * MountsStatusTask
 *
 * @author Jakub Konečný
 */
final class MountsStatusTask {
  private int $autoFeedingCost;
  
  public function __construct(private readonly ORM $orm, \Nexendrie\Model\SettingsRepository $sr) {
    $this->autoFeedingCost = $sr->settings["fees"]["autoFeedMount"];
  }

  private function decreaseHitpoints(MountEntity $mount): void {
    if($mount->autoFeed) {
      echo "Mount $mount->name ($mount->id) is fed automatically.";
      $mount->owner->money -= $this->autoFeedingCost;
    } else {
      $mount->hp -= MountEntity::HP_DECREASE_WEEKLY;
      echo "Decreasing (#$mount->id) $mount->name's life by 5.";
    }
  }

  private function makeAdult(MountEntity $mount): void {
    $twoMonths = 60 * 60 * 24 * 30 * 2;
    if($mount->gender === MountEntity::GENDER_YOUNG && $mount->created + $twoMonths < time()) {
      echo "The mount is too old. It becomes adult.";
      $roll = mt_rand(0, 1);
      $mount->gender = ($roll === 0) ? MountEntity::GENDER_MALE : MountEntity::GENDER_FEMALE;
    }
  }
  
  /**
   * @cronner-task(Mounts status update)
   * @cronner-period(1 week)
   * @cronner-time(00:00 - 01:00)
   */
  public function run(): void {
    echo "Starting mounts status update ...\n";
    $mounts = $this->orm->mounts->findOwnedMounts();
    foreach($mounts as $mount) {
      $this->decreaseHitpoints($mount);
      $this->makeAdult($mount);
      $this->orm->mounts->persist($mount);
      echo "\n";
    }
    $this->orm->flush();
    echo "Finished mounts status update ...\n";
  }
}
?>