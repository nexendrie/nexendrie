<?php
namespace Nexendrie\Orm;

/**
 * Dummy Entity
 *
 * @author Jakub Konečný
 */
abstract class DummyEntity extends \Nette\Object {
  /**
   * @return array
   */
  function toArray() {
    $return = [];
    foreach($this as $key => $value) {
      $return[$key] = $value;
    }
    return $return;
  }
}
?>