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
 * @property OneHasMany|Item[] $members {1:m User}
 */
class Group extends Entity {

}
?>