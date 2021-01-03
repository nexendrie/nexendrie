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
 * @property int $created
 * @property int $updated
 * @property int|null $returned {default null}
 * @property int $interestRate
 * @property-read string $takenT {virtual}
 * @property-read string $returnedT {virtual}
 * @property-read int $interest {virtual}
 */
final class Loan extends BaseEntity {
  protected \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterTakenT(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
  
  protected function getterReturnedT(): string {
    if($this->returned === null) {
      return "";
    }
    return $this->localeModel->formatDateTime($this->returned);
  }
  
  protected function getterInterest(): int {
    $start = $this->created;
    $end = $this->returned ?? time();
    $duration = ($end - $start) / (60 * 60 * 24);
    $interest = (int) ($this->amount * $this->interestRate * $duration / 36500);
    return max([1, $interest]);
  }
}
?>