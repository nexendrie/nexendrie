<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;


/**
 * Shop
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property OneHasMany|Item[] $items {1:m Item::$shop, orderBy=strength}
 */
class Shop extends \Nextras\Orm\Entity\Entity {

}
?>