<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * UserAdventure
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$adventures}
 * @property Adventure $adventure {m:1 Adventure::$userAdventures}
 * @property Mount $mount {m:1 Mount::$adventures}
 * @property int $started
 * @property int $progress {default 0}
 * @property int $reward {default 0}
 * @property int $loot {default 0}
 */
class UserAdventure extends \Nextras\Orm\Entity\Entity {
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->started = time();
  }
}
?>