<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Town
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property User $owner {m:1 User::$ownedTowns}
 */
class Town extends Entity {
  /**
   * @return \Nexendrie\Orm\TownDummy
   */
  function dummy() {
    return new TownDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>