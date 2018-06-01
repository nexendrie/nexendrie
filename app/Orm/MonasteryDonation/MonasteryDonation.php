<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * MonasteryDonation
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property Monastery $monastery {m:1 Monastery::$donations}
 * @property User $user {m:1 User::$monasteryDonations}
 * @property int $amount
 * @property int $when
 */
final class MonasteryDonation extends \Nextras\Orm\Entity\Entity {
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->when = time();
  }
}
?>