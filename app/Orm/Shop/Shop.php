<?php
declare(strict_types=1);

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
final class Shop extends \Nextras\Orm\Entity\Entity {

}
?>