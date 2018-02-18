<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Marriage
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user1 {m:1 User::$sentMarriages}
 * @property User $user2 {m:1 User::$receivedMarriages}
 * @property string $status {enum self::STATUS_*} {default self::STATUS_PROPOSED}
 * @property int $divorce {default 0}
 * @property int $proposed
 * @property-read string $proposedT {virtual}
 * @property int|NULL $accepted {default NULL}
 * @property-read string|NULL $acceptedT {virtual}
 * @property int|NULL $term
 * @property-read string|NULL $termT {virtual}
 * @property int|NULL $cancelled {default NULL}
 * @property-read string|NULL $cancelledT {virtual}
 * @property int $intimacy {default 0}
 * @property-read int $level {virtual}
 * @property-read int $hpIncrease {virtual}
 */
class Marriage extends \Nextras\Orm\Entity\Entity {
  public const STATUS_PROPOSED = "proposed";
  public const STATUS_ACCEPTED = "accepted";
  public const STATUS_DECLINED = "declined";
  public const STATUS_ACTIVE = "active";
  public const STATUS_CANCELLED = "cancelled";
  public const MAX_INTIMACY = 1000;
  public const INTIMACY_FOR_LEVEL = 100;
  public const HP_INCREASE_PER_LEVEL = 2;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterDivorce(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 4) {
      return 4;
    }
    return $value;
  }
  
  protected function getterProposedT(): string {
    return $this->localeModel->formatDateTime($this->proposed);
  }
  
  protected function getterAcceptedT(): string {
    if(is_null($this->accepted)) {
      return "";
    }
    return $this->localeModel->formatDateTime($this->accepted);
  }
  
  protected function getterTermT(): string {
    if(is_null($this->term)) {
      return "";
    }
    return $this->localeModel->formatDateTime($this->term);
  }
  
  protected function getterCancelledT(): string {
    if(is_null($this->cancelled)) {
      return "";
    }
    return $this->localeModel->formatDateTime($this->cancelled);
  }
  
  protected function setterIntimacy(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > self::MAX_INTIMACY) {
      return self::MAX_INTIMACY;
    }
    return $value;
  }
  
  protected function getterLevel(): int {
    if($this->status != self::STATUS_ACTIVE) {
      return 0;
    }
    return (int) ($this->intimacy / self::INTIMACY_FOR_LEVEL);
  }
  
  protected function getterHpIncrease(): int {
    return $this->level * self::HP_INCREASE_PER_LEVEL;
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->proposed = time();
  }
  
  protected function onBeforeUpdate() {
    parent::onBeforeUpdate();
    if($this->status === self::STATUS_ACCEPTED AND is_null($this->accepted)) {
      $this->accepted = time();
    }
    if($this->status === self::STATUS_ACCEPTED AND is_null($this->term)) {
      $this->term = time() + (60 * 60 * 24 * 14);
    }
    if($this->status === self::STATUS_DECLINED AND is_null($this->accepted)) {
      $this->accepted = time();
    }
    if($this->status === self::STATUS_CANCELLED AND is_null($this->cancelled)) {
      $this->cancelled = time();
    }
  }
}
?>