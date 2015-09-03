<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Item
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $price
 * @property Shop $shop {m:1 Shop}
 * @property OneHasMany|UserItem[] $userItems {1:m UserItem::$item}
 */
class Item extends Entity {

}
?>