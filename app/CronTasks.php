<?php
namespace Nexendrie;

use Nexendrie\Orm\Mount,
    Nexendrie\Orm\Marriage;

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
  /** @var \Nexendrie\Model\Marriage */
  protected $marriageModel;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\Taxes $taxesModel, \Nexendrie\Model\Marriage $marriageModel) {
    $this->orm = $orm;
    $this->taxesModel = $taxesModel;
    $this->marriageModel = $marriageModel;
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
    $twoMonths = 60 * 60 * 24 * 30 * 2;
    echo "Starting mounts status update ...\n";
    $mounts = $this->orm->mounts->findOwnedMounts();
    foreach($mounts as $mount) {
      $mount->hp -= 5;
      echo "Decreasing (#$mount->id) $mount->name's life by 5.";
      if($mount->gender === Mount::GENDER_YOUNG AND $mount->birth + $twoMonths < time()) {
        echo "The mount is too old. It becomes adult.";
        $roll = mt_rand(0, 1);
        if($roll === 0) $mount->gender = Mount::GENDER_MALE;
        else $mount->gender = Mount::GENDER_FEMALE;
      }
      $this->orm->mounts->persist($mount);
      echo "\n";
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
   * Guild fees
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Guild fees
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  function guildFees() {
    $date = new \DateTime;
    $date->setTimestamp(time());
    if($date->format("j") != 1) return;
    echo "Starting paying guild fees ...\n";
    $users = $this->orm->users->findInGuild();
    foreach($users as $user) {
      $guildFee = $user->guildRank->guildFee;
      echo "$user->publicname (#$user->id} will pay {$guildFee} to his/her guild.\n";
      $user->money -= $guildFee;
      $user->guild->money += $guildFee;
      $this->orm->users->persistAndFlush($user);
    }
    echo "Finished paying guild fees ...\n";
  }
  
  /**
   * Guild fees
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Order fees
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  function orderFees() {
    $date = new \DateTime;
    $date->setTimestamp(time());
    if($date->format("j") != 1) return;
    echo "Starting paying order fees ...\n";
    $users = $this->orm->users->findInOrder();
    foreach($users as $user) {
      $orderFee = $user->orderRank->orderFee;
      echo "$user->publicname (#$user->id} will pay {$orderFee} to his/her order.\n";
      $user->money -= $orderFee;
      $user->guild->money += $orderFee;
      $this->orm->users->persistAndFlush($user);
    }
    echo "Finished paying order fees ...\n";
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
  
  /**
   * Castles status update
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Castles status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  function castlesStatus() {
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
  
  /**
   * Houses status update
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Houses status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  function housesStatus() {
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
  
  /**
   * Close weddings
   * 
   * @author Jakub Konečný
   * @return void
   * 
   * @cronner-task Close weddings
   * @cronner-period 1 hour
   */
  function closeWeddings() {
    echo "Starting closing weddings ...\n";
    $weddings = $this->orm->marriages->findOpenWeddings();
    foreach($weddings as $wedding) {
      if(!$this->marriageModel->canFinish($wedding)) {
        echo "Wedding (#$wedding->id) cannot be finished!\n";
      } else {
        echo "Closed wedding (#$wedding->id).\n";
        $wedding->status = Marriage::STATUS_ACTIVE;
        $this->orm->marriages->persist($wedding);
      }
    }
    $this->orm->flush();
    echo "Finished closing weddings ...\n";
  }
}
?>