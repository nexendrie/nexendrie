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
  
  /**
   * @return array
   */
  function toArray(): array {
    $return = [];
    foreach($this as $key => $value) {
      $return[$key] = $value;
    }
    return $return;
  }
}
?>