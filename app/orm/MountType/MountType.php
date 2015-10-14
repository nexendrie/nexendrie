<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * MountType
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $level
 * @property OneHasMany|Mount[] $mounts {1:m Mount::$type}
 */
class MountType extends Entity {
  
}
?>