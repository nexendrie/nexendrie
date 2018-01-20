<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Orm\Mount as MountEntity;

/**
 * MountsStatusTask
 *
 * @author Jakub Konečný
 */
class MountsStatusTask {
  use \Nette\SmartObject;
  
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  protected function decreaseHitpoints(MountEntity $mount): void {
    $mount->hp -= 5;
    echo "Decreasing (#$mount->id) $mount->name's life by 5.";
  }
  
  protected function makeAdult(MountEntity $mount): void {
    $twoMonths = 60 * 60 * 24 * 30 * 2;
    if($mount->gender === MountEntity::GENDER_YOUNG AND $mount->birth + $twoMonths < time()) {
      echo "The mount is too old. It becomes adult.";
      $roll = mt_rand(0, 1);
      $mount->gender = ($roll === 0) ? MountEntity::GENDER_MALE : MountEntity::GENDER_FEMALE;
    }
  }
  
  /**
   * @cronner-task Mounts status update
   * @cronner-period 1 week
   * @cronner-time 00:00 - 01:00
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