<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Notification|null getById(int $id)
 * @method Notification|null getBy(array $conds)
 * @method ICollection|Notification[] findBy(array $conds)
 * @method ICollection|Notification[] findAll()
 */
final class NotificationsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Notification::class];
  }
  
  /**
   * @return ICollection|Notification[]
   */
  public function findByUser(User|int $user): ICollection {
    return $this->findBy(["user" => $user]);
  }
}
?>