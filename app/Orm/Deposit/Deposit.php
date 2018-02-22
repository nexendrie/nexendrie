<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Deposit
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$deposits}
 * @property int $amount
 * @property int $opened
 * @property int $term
 * @property bool $closed {default false}
 * @property int $interestRate
 * @property-read string $termT {virtual}
 * @property-read int $interest {virtual}
 * @property-read bool $due {virtual}
 */
class Deposit extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterTermT(): string {
    return $this->localeModel->formatDateTime($this->term);
  }
  
  protected function getterInterest(): int {
    $start = $this->opened;
    $end = $this->term;
    $duration = ($end - $start) / (60 * 60 * 24);
    $interest = (int) ($this->amount * $this->interestRate * $duration / 36500);
    return max([1, $interest]);
  }
  
  protected function getterDue(): bool {
    return (time() >= $this->term);
  }
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->opened = time();
  }
  
}
?>