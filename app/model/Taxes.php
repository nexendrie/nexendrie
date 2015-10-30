<?php
namespace Nexendrie\Model;

/**
 * Taxes Model
 *
 * @author Jakub Konečný
 */
class Taxes extends \Nette\Object {
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
   * Calculate tax
   * 
   * @param int $income
   * @return int
   */
  function calculateTax($income) {
    return (int) round(@($income / 100 * $this->taxRate));
  }
  
  /**
   * Calculate specified user's income from a month
   * 
   * @param int $user
   * @param int $month
   * @param int $year
   * @return int[]
   */
  function calculateIncome($user, $month = 0, $year = 0) {
    if($month === 0) $month = date("n");
    if($year === 0) $year = date("Y");
    $workIncome = $this->jobModel->calculateMonthJobIncome($user, $month, $year);
    $adventuresIncome = 0;
    return array("work" => $workIncome, "adventures" => $adventuresIncome);
  }
  
  /**
   * Calculate taxes for a town
   * 
   * @param \Nexendrie\Orm\Town $town
   * @param int $month
   * @param int $year
   * @return \stdClass
   */
  function calculateTownTaxes(\Nexendrie\Orm\Town $town, $month = 0, $year = 0) {
    $return = (object) array(
      "id" => $town->id, "name" => $town->name, "owner" => 0,
      "taxes" => 0, "denizens" => array()
    );
    if($month === 0) $month = date("n");
    if($year === 0) $year = date("Y");
    $return->owner = $town->owner->id;
    foreach($town->denizens as $denizen) {
      if($denizen->id === 0) continue;
      $d = (object) array(
        "id" => $denizen->id, "publicname" => $denizen->publicname,
        "income" => 0, "tax" => 0
      );
      if($town->owner->id === $denizen->id) {
        $return->denizens[$d->id] = $d;
        continue;
      }
      $d->income = array_sum($this->calculateIncome($denizen->id, $month, $year));
      $d->tax = $this->calculateTax($d->income);
      $return->denizens[$d->id] = $d;
      $return->taxes += $d->tax;
    }
    return $return;
  }
  
  /**
   * @return array
   */
  function payTaxes() {
    $return = array();
    $date = new \DateTime;
    $date->setTimestamp(time());
    $date->modify("-1 day");
    $month = $date->format("n");
    $year = $date->format("Y");
    if($month === 0) $month = date("n");
    if($year === 0) $year = date("Y");
    /* @var $town \Nexendrie\Orm\Town */
    foreach($this->orm->towns->findAll() as $town) {
      $result = $this->calculateTownTaxes($town, $month, $year);
      $return[] = $result;
      if($result->taxes > 0) {
        $town->owner->money += $result->taxes;
        foreach($town->denizens as $denizen) {
          $denizen->money -= $result->denizens[$denizen->id]->tax;
        }
        $this->orm->towns->persistAndFlush($town);
      }
    }
    return $return;
  }
}
?>