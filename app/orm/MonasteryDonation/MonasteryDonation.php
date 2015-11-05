<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * MonasteryDonation
 *
 * @author Jakub Konečný
 * @property Monastery $monastery {m:1 Monastery::$donations}
 * @property User $user {m:1 User::$monasteryDonations}
 * @property int $amount
 * @property int $when
 */
class MonasteryDonation extends Entity {
  
}
?>