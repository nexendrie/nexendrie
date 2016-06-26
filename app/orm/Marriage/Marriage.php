<?php
namespace Nexendrie\Orm;

/**
 * Marriage
 *
 * @author Jakub Konečný
 * @property User $user1 {m:1 User::$sentMarriages}
 * @property User $user2 {m:1 User::$recievedMarriages}
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
 */
class Marriage extends \Nextras\Orm\Entity\Entity {
  const STATUS_PROPOSED = "proposed";
  const STATUS_ACCEPTED = "accepted";
  const STATUS_DECLINED = "declined";
  const STATUS_ACTIVE = "active";
  const STATUS_CANCELLED = "cancelled";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterDivorce($value) {
    if($value < 0) return 0;
    elseif($value > 4) return 4;
    else return $value;
  }
  
  protected function getterProposedT() {
    return $this->localeModel->formatDateTime($this->proposed);
  }
  
  protected function getterAcceptedT() {
    if($this->accepted === NULL) return "";
    else return $this->localeModel->formatDateTime($this->accepted);
  }
  
  protected function getterTermT() {
    if($this->term === NULL) return "";
    else return $this->localeModel->formatDateTime($this->term);
  }
  
  protected function getterCancelledT() {
    if($this->cancelled === NULL) return "";
    else return $this->localeModel->formatDateTime($this->cancelled);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->proposed = time();
  }
  
  protected function onBeforeUpdate() {
    parent::onBeforeUpdate();
    if($this->status === self::STATUS_ACCEPTED AND is_null($this->accepted)) $this->accepted = time();
    if($this->status === self::STATUS_ACCEPTED AND is_null($this->term)) $this->term = time() + (60 * 60 * 24 * 14);
    if($this->status === self::STATUS_DECLINED AND is_null($this->accepted)) $this->accepted = time();
    if($this->status === self::STATUS_CANCELLED AND is_null($this->cancelled)) $this->cancelled = time();
  }
}
?>