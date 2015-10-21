<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Loan
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$loans}
 * @property int $amount
 * @property int $taken
 * @property int|NULL $returned {default NULL}
 * @property int $interest
 * @property-read string $takenT {virtual}
 * @property-read string $returnedT {virtual}
 */
class Loan extends Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterTakenT() {
    return $this->localeModel->formatDateTime($this->taken);
  }
  
  function getterReturnedT() {
    if($this->returned === NULL) return "";
    else return $this->localeModel->formatDateTime($this->returned);
  }
}
?>