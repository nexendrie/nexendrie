<?php
namespace Nexendrie\Orm;

/**
 * BeerProduction
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$beerProduction}
 * @property House $house {m:1 House::$beerProduction}
 * @property int $amount
 * @property int $price
 * @property int $when
 */
class BeerProduction extends \Nextras\Orm\Entity\Entity {

}
?>