<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * MountType
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property-read string $maleName {virtual}
 * @property string $femaleName
 * @property string $youngName
 * @property string $description
 * @property int $level
 * @property int $damage {default 0}
 * @property int $armor {default 0}
 * @property int $price
 * @property OneHasMany|Mount[] $mounts {1:m Mount::$type}
 */
final class MountType extends BaseEntity {
  protected function getterMaleName(): string {
    return $this->name;
  }
}
?>