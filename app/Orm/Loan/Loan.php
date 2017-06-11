<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Loan
 *
 * @author Jakub Konečný
 * @property int $id {primary}
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
  
  protected function getterAmountT(): string {
    return $this->localeModel->money($this->amount);
  }
  
  protected function getterTakenT(): string {
    return $this->localeModel->formatDateTime($this->taken);
  }
  
  protected function getterReturnedT(): string {
    if(is_null($this->returned)) {
      return "";
    }
    return $this->localeModel->formatDateTime($this->returned);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->taken = time();
  }
}
?>