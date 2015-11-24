<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;


/**
 * Shop
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property OneHasMany|Item[] $items {1:m Item order:strength}
 */
class Shop extends \Nextras\Orm\Entity\Entity {

}
?>