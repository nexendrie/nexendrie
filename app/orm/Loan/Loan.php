<?php
namespace Nexendrie\Orm;

/**
 * Loan
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$loans}
 * @property int $amount
 * @property int $taken
 * @property int|NULL $returned {default NULL}
 * @property int $interest
 * @property string $amountT {virtual}
 * @property-read string $takenT {virtual}
 * @property-read string $returnedT {virtual}
 */
class Loan extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterAmountT() {
    return $this->localeModel->money($this->amount);
  }
  
  protected function getterTakenT() {
    return $this->localeModel->formatDateTime($this->taken);
  }
  
  protected function getterReturnedT() {
    if($this->returned === NULL) return "";
    else return $this->localeModel->formatDateTime($this->returned);
  }
}
?>