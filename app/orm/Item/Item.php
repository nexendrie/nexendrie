<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Post
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $price
 * @property Shop $shop {m:1 Shop}
 */
class Item extends Entity {

}
?>