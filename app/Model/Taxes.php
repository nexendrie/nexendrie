<?php
declare(strict_types=1);

namespace Nexendrie\Model;

/**
 * Taxes Model
 *
 * @author Jakub Konečný
 */
final class Taxes {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var Job */
  protected $jobModel;
  /** @var Adventure */
  protected $adventureModel;
  /** @var int */
  protected $taxRate;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, Job $jobModel, Adventure $adventureModel, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->jobModel = $jobModel;
    $this->adventureModel = $adventureModel;
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
    if(is_null($month)) {
      $month = (int) date("n");
    }
    if(is_null($year)) {
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
      /** @var \stdClass $d */
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
    if(!is_null($castle)) {
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
    /** @var \Nexendrie\Orm\Town $town */
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