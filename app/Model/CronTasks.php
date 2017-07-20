<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Utils\Arrays,
    Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Orm\Marriage as MarriageEntity,
    Nexendrie\Orm\ElectionResult;

/**
 * Cron Tasks
 *
 * @author Jakub Konečný
 */
class CronTasks {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var Taxes */
  protected $taxesModel;
  /** @var Marriage */
  protected $marriageModel;
  /** @var Elections */
  protected $electionsModel;
  
  public function __construct(\Nexendrie\Orm\Model $orm, Taxes $taxesModel, Marriage $marriageModel, Elections $electionsModel) {
    $this->orm = $orm;
    $this->taxesModel = $taxesModel;
    $this->marriageModel = $marriageModel;
    $this->electionsModel = $electionsModel;
  }
  
  /**
   * Mounts status update
   *
   * @cronner-task Mounts status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  public function mountsStatus(): void {
    $twoMonths = 60 * 60 * 24 * 30 * 2;
    echo "Starting mounts status update ...\n";
    $mounts = $this->orm->mounts->findOwnedMounts();
    foreach($mounts as $mount) {
      $mount->hp -= 5;
      echo "Decreasing (#$mount->id) $mount->name's life by 5.";
      if($mount->gender === MountEntity::GENDER_YOUNG AND $mount->birth + $twoMonths < time()) {
        echo "The mount is too old. It becomes adult.";
        $roll = mt_rand(0, 1);
        $mount->gender = ($roll === 0) ? MountEntity::GENDER_MALE : MountEntity::GENDER_FEMALE;
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
   * @cronner-task Taxes
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  public function taxes(): void {
    $date = new \DateTime;
    $date->setTimestamp(time());
    if($date->format("j") != 1) {
      return;
    }
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
   * @cronner-task Guild fees
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  public function guildFees(): void {
    $date = new \DateTime;
    $date->setTimestamp(time());
    if($date->format("j") != 1) {
      return;
    }
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
   * @cronner-task Order fees
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  public function orderFees(): void {
    $date = new \DateTime;
    $date->setTimestamp(time());
    if($date->format("j") != 1) {
      return;
    }
    echo "Starting paying order fees ...\n";
    $users = $this->orm->users->findInOrder();
    foreach($users as $user) {
      $orderFee = $user->orderRank->orderFee;
      echo "$user->publicname (#$user->id} will pay {$orderFee} to his/her order.\n";
      $user->money -= $orderFee;
      $user->order->money += $orderFee;
      $this->orm->users->persistAndFlush($user);
    }
    echo "Finished paying order fees ...\n";
  }
  
  /**
   * Close adventures
   *
   * @cronner-task Close adventures
   * @cronner-period 1 day
   * @cronner-time 00:00 - 01:00
   */
  public function closeAdventures(): void {
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
   * @cronner-task Monasteries status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  public function monasteriesStatus(): void {
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
   * @cronner-task Castles status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  public function castlesStatus(): void {
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
   * @cronner-task Houses status update
   * @cronner-period 1 week
   * @cronner-time 01:00 - 02:00
   */
  public function housesStatus(): void {
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
   * @cronner-task Close weddings
   * @cronner-period 1 hour
   */
  public function closeWeddings(): void {
    echo "Starting closing weddings ...\n";
    $weddings = $this->orm->marriages->findOpenWeddings();
    foreach($weddings as $wedding) {
      if(!$this->marriageModel->canFinish($wedding)) {
        echo "Wedding (#$wedding->id) cannot be finished!\n";
        continue;
      }
      echo "Closed wedding (#$wedding->id).\n";
      $wedding->status = MarriageEntity::STATUS_ACTIVE;
      $this->orm->marriages->persist($wedding);
    }
    $this->orm->flush();
    echo "Finished closing weddings ...\n";
  }
  
  protected function getElectionResults(int $town, int $year, int $month): array {
    $votes = $this->orm->elections->findVotedInMonth($town, $year, $month);
    $results = [];
    foreach($votes as $vote) {
      if(!in_array($vote->candidate->id, $this->electionsModel->getCandidates($town)->fetchPairs(NULL, "id"))) {
        continue;
      }
      $index = $vote->candidate->username;
      if(!isset($results[$index])) {
        $results[$index] = [
          "candidate" => $vote->candidate, "amount" => 0
        ];
      }
      $results[$index]["amount"]++;
    }
    return Arrays::orderby($results, "amount", SORT_DESC);
  }
  
  /**
   * Municipal elections
   *
   * @cronner-task Municipal elections
   * @cronner-period 1 day
   * @cronner-time 01:00 - 02:00
   */
  public function municipalElections(): void {
    $date = new \DateTime;
    $date->setTimestamp(time());
    if($date->format("j") != 1) {
      return;
    }
    echo "Starting proccessing results of municipal elections ...\n";
    $date->modify("-1 day");
    $year = (int) $date->format("Y");
    $month = (int) $date->format("n");
    $towns = $this->orm->towns->findAll();
    foreach($towns as $town) {
      echo "Town (#$town->id) $town->name ...\n";
      $councillors = $this->electionsModel->getNumberOfCouncillors($town->id);
      $results = $this->getElectionResults($town->id, $year, $month);
      if(!count($results)) {
        echo "No votes found.\n";
        continue;
      }
      echo sprintf("Found %d possible candidates, the town can have %d councillors.\n", count($results), $councillors);
      foreach($results as $row) {
        $record = new ElectionResult;
        $record->candidate = $row["candidate"];
        $record->town = $town;
        $record->votes = $row["amount"];
        $record->year = $year;
        $record->month = $month;
        if($councillors <= 0) {
          echo "{$row["candidate"]->publicname} will not become a councillor.\n";
          $this->orm->electionResults->persist($record);
          continue;
        }
        echo "{$row["candidate"]->publicname} will become a councillor.\n";
        $record->elected = true;
        $record->candidate->group = $this->orm->groups->getByLevel(300);
        $this->orm->electionResults->persist($record);
        $councillors--;
      }
    }
    $this->orm->flush();
    echo "Finished proccessing results of municipal elections ...\n";
  }
}
?>