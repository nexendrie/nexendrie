<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Election
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $candidate {m:1 User::$receivedVotes}
 * @property User $voter {m:1 User::$castedVotes}
 * @property Town $town {m:1 Town::$elections}
 * @property int $when
 * @property bool $elected {default false}
 */
class Election extends \Nextras\Orm\Entity\Entity {
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->when = time();
  }
}
?>