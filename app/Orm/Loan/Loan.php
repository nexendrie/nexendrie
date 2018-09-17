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
 * @property int|null $returned {default null}
 * @property int $interestRate
 * @property string $amountT {virtual}
 * @property-read string $takenT {virtual}
 * @property-read string $returnedT {virtual}
 * @property-read int $interest {virtual}
 */
final class Loan extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
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
  
  protected function getterInterest(): int {
    $start = $this->taken;
    $end = ($this->returned) ? $this->returned : time();
    $duration = ($end - $start) / (60 * 60 * 24);
    $interest = (int) ($this->amount * $this->interestRate * $duration / 36500);
    return max([1, $interest]);
  }
  
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    $this->taken = time();
  }
}
?>