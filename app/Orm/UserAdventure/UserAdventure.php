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
 * @property-read AdventureNpc|NULL $nextEnemy {virtual}
 */
final class UserAdventure extends \Nextras\Orm\Entity\Entity {
  public const PROGRESS_COMPLETED = 10;
  public const PROGRESS_CLOSED = 11;
  
  protected function getterNextEnemy(): ?AdventureNpc {
    if($this->progress >= static::PROGRESS_COMPLETED) {
      return NULL;
    }
    return $this->adventure->npcs->get()->getBy([
      "order" => $this->progress + 1
    ]);
  }
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->started = time();
  }
}
?>