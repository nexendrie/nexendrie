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

  protected function getterCreated(?int $value): int {
    return $value ?? time();
  }
}
?>