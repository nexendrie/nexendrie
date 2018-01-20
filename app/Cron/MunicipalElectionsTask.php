<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\Elections,
    Nexendrie\Utils\Arrays,
    Nexendrie\Orm\ElectionResult;

/**
 * MunicipalElectionsTask
 *
 * @author Jakub Konečný
 */
class MunicipalElectionsTask extends BaseMonthlyCronTask {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var Elections */
  protected $electionsModel;
  
  public function __construct(\Nexendrie\Orm\Model $orm, Elections $electionsModel) {
    $this->orm = $orm;
    $this->electionsModel = $electionsModel;
  }
  
  protected function getElectionResults(int $town, int $year, int $month): array {
    $votes = $this->orm->elections->findVotedInMonth($town, $year, $month);
    $results = [];
    foreach($votes as $vote) {
      if(!in_array($vote->candidate->id, $this->electionsModel->getCandidates($town)->fetchPairs(NULL, "id"), true)) {
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
   * @cronner-task Municipal elections
   * @cronner-period 1 day
   * @cronner-time 00:00 - 01:00
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
    foreach($towns as $town) {
      echo "Town (#$town->id) $town->name ...\n";
      $councillors = $this->electionsModel->getNumberOfCouncillors($town->id);
      $results = $this->getElectionResults($town->id, $year, $month);
      if(count($results) === 0) {
        echo "No votes found.\n";
        continue;
      }
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