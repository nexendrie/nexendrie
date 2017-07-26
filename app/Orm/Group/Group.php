<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;


/**
 * Group
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $singleName
 * @property string $femaleName
 * @property int $level
 * @property string $path {enum self::PATH_*} 
 * @property OneHasMany|User[] $members {1:m User::$group}
 * @property OneHasMany|Permission[] $permissions {1:m Permission::$group}
 */
class Group extends \Nextras\Orm\Entity\Entity {
  const PATH_CITY = "city";
  const PATH_CHURCH = "church";
  const PATH_TOWER = "tower";
  
  public function dummy(): GroupDummy {
    return new GroupDummy($this);
  }
}
?>