<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;


/**
 * Group
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $singleName
 * @property int $level
 * @property int $path {enum self::PATH_*} 
 * @property OneHasMany|User[] $members {1:m User}
 * @property OneHasMany|Permission[] $permissions {1:m Permission}
 */
class Group extends Entity {
  const PATH_CITY = 1;
  const PATH_CHURCH = 2;
  const PATH_TOWER = 3;
  
  /**
   * @return \Nexendrie\Orm\GroupDummy
   */
  function dummy() {
    return new GroupDummy($this);
  }
}
?>