<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Mount|null getById(int $id)
 * @method Mount|null getBy(array $conds)
 * @method ICollection|Mount[] findBy(array $conds)
 * @method ICollection|Mount[] findAll()
 */
final class MountsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Mount::class];
  }
  
  /**
   * @param User|int $owner
   * @return ICollection|Mount[]
   */
  public function findByOwner($owner): ICollection {
    return $this->findBy(["owner" => $owner]);
  }

  /**
    * @param User|int $owner
    * @return ICollection|Mount[]
    */
  public function findAutoFed($owner): ICollection {
    return $this->findBy(["owner" => $owner, "autoFeed" => true, ]);
  }
  
  /**
   * @param MountType|int $type
   * @return ICollection|Mount[]
   */
  public function findByType($type): ICollection {
    return $this->findBy(["type" => $type]);
  }
  /**
   * Get mounts on market
   * 
   * @return ICollection|Mount[]
   */
  public function findOnMarket(): ICollection {
    return $this->findBy(["onMarket" => true]);
  }
  
  /**
   * Get mounts owned by users
   * 
   * @return ICollection|Mount[]
   */
  public function findOwnedMounts(): ICollection {
    return $this->findBy(["owner->id>" => 0]);
  }
  
  /**
   * Get mounts of specified user for adventure
   * 
   * @param int $user
   * @return ICollection|Mount[]
   */
  public function findGoodMounts($user): ICollection {
    return $this->findBy(["owner->id" => $user, "hp>" => 30]);
  }
}
?>