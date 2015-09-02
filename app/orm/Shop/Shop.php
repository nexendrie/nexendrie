<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;


/**
 * Post
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property OneHasMany|Item[] $items {1:m Item}
 */
class Shop extends Entity {

}
?>