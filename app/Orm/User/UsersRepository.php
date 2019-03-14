<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ICollection|User[] findByLikeName(string $publicname)
 */
final class UsersRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [User::class];
  }
  
  /**
   * @param int $id
   * @return User|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByPublicname(string $publicname): ?User {
    return $this->getBy(["publicname" => $publicname]);
  }
  
  public function getByEmail(string $email): ?User {
    return $this->getBy(["email" => $email]);
  }
  
  /**
   * @param Group|Int $group
   * @return ICollection|User[]
   */
  public function findByGroup($group): ICollection {
    return $this->findBy(["group" => $group]);
  }
  
  /**
   * @param Monastery|Int $monastery
   * @return ICollection|User[]
   */
  public function findByMonastery($monastery): ICollection {
    return $this->findBy(["monastery" => $monastery]);
  }
  
  /**
   * Get mayor of a town
   */
  public function getTownMayor(int $town): ?User {
    return $this->getBy(["town" => $town, "this->group->level" => 345]);
  }
  
  /**
   * Get citizens of specified town
   *
   * @return ICollection|User[]
   */
  public function findTownCitizens(int $town): ICollection {
    return $this->findBy([
      "town" => $town,
      "this->group->level" => [100, 300]
    ]);
  }
  
  /**
   * Get peasants from specified town
   *
   * @return ICollection|User[]
   */
  public function findTownPeasants(int $town): ICollection {
    return $this->findBy([
      "town" => $town,
      "this->group->level" => [50]
    ]);
  }
  
  /**
   * Get users in guild
   * 
   * @return ICollection|User[]
   */
  public function findInGuild(): ICollection {
    return $this->findBy([
      "guild!=" => null,
      "this->group->path" => Group::PATH_CITY
    ]);
  }
  
  /**
   * Get users in order
   * 
   * @return ICollection|User[]
   */
  public function findInOrder(): ICollection {
    return $this->findBy([
      "order!=" => null,
      "this->group->path" => Group::PATH_TOWER
    ]);
  }
  
  /**
   * Get members of specified order
   *
   * @return ICollection|User[]
   */
  public function findByOrder(int $order): ICollection {
    return $this->findBy(["order" => $order])
      ->orderBy("orderRank", ICollection::DESC);
  }
  
  /**
   * Get members of specified guild
   *
   * @return ICollection|User[]
   */
  public function findByGuild(int $guild): ICollection {
    return $this->findBy(["guild" => $guild])
      ->orderBy("guildRank", ICollection::DESC);
  }
}
?>