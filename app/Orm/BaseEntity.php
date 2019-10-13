<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

abstract class BaseEntity extends \Nextras\Orm\Entity\Entity {
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    if($this->metadata->hasProperty("created")) {
      $this->created = time();
    }
  }

  public function onBeforeUpdate() {
    parent::onBeforeUpdate();
    if($this->metadata->hasProperty("updated")) {
      $this->updated = time();
    }
  }

  protected function getterCreated(?int $value): int {
    if($value === 0 || $value === null) {
      return time();
    }
    return $value;
  }

  protected function getterUpdated(?int $value): int {
    if($value === 0 || $value === null) {
      if($this->metadata->hasProperty("created")) {
        return $this->created;
      }
      return time();
    }
    return $value;
  }
}
?>