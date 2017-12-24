<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Dummy Entity
 *
 * @author Jakub Konečný
 */
abstract class DummyEntity {
  use \Nette\SmartObject;
  
  public function toArray(): array {
    $return = [];
    foreach(get_object_vars($this) as $key => $value) {
      $return[$key] = $value;
    }
    return $return;
  }
}
?>