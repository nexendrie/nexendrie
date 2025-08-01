<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Model as ORM;

/**
 * Taxes Model
 *
 * @author Jakub Konečný
 */
final class Taxes {
  private int $taxRate;
  
  public function __construct(private readonly ORM $orm, private readonly Job $jobModel, private readonly Adventure $adventureModel, SettingsRepository $sr) {
    $this->taxRate = $sr->settings["fees"]["incomeTax"];
  }
  
  /**
   * Calculate tax
   */
  public function calculateTax(int $income): int {
    return (int) round($income / 100 * $this->taxRate);
  }
  
  /**
   * Calculate specified user's income from a month
   *
   * @return int[]
   */
  public function calculateIncome(int $user, int $month = null, int $year = null): array {
    if($month === null) {
      $month = (int) date("n");
    }
    if($year === null) {
      $year = (int) date("Y");
    }
    $workIncome = $this->jobModel->calculateMonthJobIncome($user, $month, $year);
    $adventuresIncome = $this->adventureModel->calculateMonthAdventuresIncome($user, $month, $year);
    return ["work" => $workIncome, "adventures" => $adventuresIncome];
  }
  
  /**
   * Calculate taxes for a town
   */
  public function calculateTownTaxes(\Nexendrie\Orm\Town $town, int $month = null, int $year = null): \stdClass {
    $return = (object) [
      "id" => $town->id, "name" => $town->name, "owner" => 0,
      "taxes" => 0, "denizens" => []
    ];
    $return->owner = $town->owner->id;
    foreach($town->denizens as $denizen) {
      if($denizen->id === 0) {
        continue;
      }
      $d = (object) [
        "id" => $denizen->id, "publicname" => $denizen->publicname,
        "income" => 0, "tax" => 0
      ];
      if($town->owner->id === $denizen->id) {
        $return->denizens[$d->id] = $d;
        continue;
      }
      $d->income = array_sum($this->calculateIncome($denizen->id, $month ?? (int) date("n"), $year ?? (int) date("Y")));
      $d->tax = $this->calculateTax($d->income);
      $return->denizens[$d->id] = $d;
      $return->taxes += $d->tax;
    }
    $castle = $this->orm->castles->getByOwner($town->owner->id);
    if($castle !== null) {
      $return->taxes += $castle->taxesBonusIncome;
    }
    return $return;
  }
  
  public function payTaxes(): array {
    $return = [];
    $date = new \DateTime();
    $date->setTimestamp(time());
    $date->modify("-1 day");
    $month = (int) $date->format("n");
    $year = (int) $date->format("Y");
    foreach($this->orm->towns->findAll() as $town) {
      $result = $this->calculateTownTaxes($town, $month, $year);
      $return[] = $result;
      if($result->taxes > 0) {
        $town->owner->money += $result->taxes;
        foreach($town->denizens as $denizen) {
          if(!isset($result->denizens[$denizen->id])) {
            continue;
          }
          $denizen->money -= $result->denizens[$denizen->id]->tax;
        }
        $this->orm->towns->persistAndFlush($town);
      }
    }
    return $return;
  }
}
?>