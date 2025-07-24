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
 * @property int $created
 * @property int $progress {default 0}
 * @property int $reward {default 0}
 * @property int $loot {default 0}
 * @property-read AdventureNpc|null $nextEnemy {virtual}
 */
final class UserAdventure extends BaseEntity {
  public const PROGRESS_COMPLETED = 10;
  public const PROGRESS_CLOSED = 11;
  
  protected function getterNextEnemy(): ?AdventureNpc {
    if($this->progress >= self::PROGRESS_COMPLETED) {
      return null;
    }
    return $this->adventure->npcs->toCollection()->getBy([ // @phpstan-ignore return.type
      "order" => $this->progress + 1
    ]);
  }
}
?>