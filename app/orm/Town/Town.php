<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Town
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property User $owner {m:1 User::$ownedTowns}
 * @property int $price {default 5000}
 * @property OneHasMany|User[] $denizens {1:m User::$town}
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