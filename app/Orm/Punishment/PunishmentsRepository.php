<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class PunishmentsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Punishment::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Punishment {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|Punishment
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * Find specified user's active punishment
   * 
   * @param int $user User's id
   */
  public function getActivePunishment(int $user): ?Punishment {
    return $this->getBy(["user" => $user, "released" => NULL]);
  }
}
?>