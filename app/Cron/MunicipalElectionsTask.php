<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\Elections;
use Nexendrie\Orm\ElectionResult;
use Nexendrie\Orm\Group;
use Nexendrie\Orm\Model as ORM;

/**
 * MunicipalElectionsTask
 *
 * @author Jakub Konečný
 */
final class MunicipalElectionsTask extends BaseMonthlyCronTask {
  public function __construct(private readonly ORM $orm, private readonly Elections $electionsModel) {
  }

  private function getElectionResults(int $town, int $year, int $month): array {
    $votes = $this->orm->elections->findVotedInMonth($town, $year, $month);
    $results = [];
    foreach($votes as $vote) {
      if(!in_array($vote->candidate->id, $this->electionsModel->getCandidates($town)->fetchPairs(null, "id"), true)) {
        continue;
      }
      $index = $vote->candidate->publicname;
      if(!isset($results[$index])) {
        $results[$index] = [
          "candidate" => $vote->candidate, "amount" => 0,
        ];
      }
      $results[$index]["amount"]++;
    }
    usort($results, function($a, $b): int {
      return $a["amount"] <=> $b["amount"];
    });
    return $results;
  }
  
  /**
   * @cronner-task(Municipal elections)
   * @cronner-period(1 day)
   * @cronner-time(00:00 - 01:00)
   */
  public function run(): void {
    $date = new \DateTime();
    $date->setTimestamp(time());
    if(!$this->isDue($date)) {
      return;
    }
    echo "Starting proccessing results of municipal elections ...\n";
    $date->modify("-1 day");
    $year = (int) $date->format("Y");
    $month = (int) $date->format("n");
    $towns = $this->orm->towns->findAll();
    $ranks = $this->orm->groups->getCityGroupIds();
    foreach($towns as $town) {
      echo "Town (#$town->id) $town->name ...\n";
      $councillors = $this->electionsModel->getNumberOfCouncillors($town->id);
      $results = $this->getElectionResults($town->id, $year, $month);
      if(count($results) === 0) {
        echo "No votes found.\n";
        continue;
      }
      $newCouncillors = [];
      echo sprintf("Found %d possible candidates, the town can have %d councillors.\n", count($results), $councillors);
      foreach($results as $row) {
        $record = new ElectionResult();
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
        $newCouncillors[] = $row["candidate"]->id;
        $record->elected = true;
        $record->candidate->group = $ranks[1];
        $this->orm->electionResults->persist($record);
        $councillors--;
      }
      foreach($town->denizens as $denizen) {
        if($denizen->group->id !== $ranks[1] || in_array($denizen->id, $newCouncillors, true)) {
          continue;
        }
        echo "$denizen->publicname loses his/her seat in town council.\n";
        /** @var Group $group */
        $group = $this->orm->groups->getByLevel($ranks[2]);
        $denizen->group = $group;
        $this->orm->users->persist($denizen);
      }
    }
    $this->orm->flush();
    echo "Finished proccessing results of municipal elections ...\n";
  }
}
?>