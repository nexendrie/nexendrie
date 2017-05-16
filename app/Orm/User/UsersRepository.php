<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class UsersRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [User::class];
  }
  
  /**
   * @param int $id
   * @return User|NULL
   */
  function getById($id): ?User {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param string $username
   * @return User|NULL
   */
  function getByUsername(string $username): ?User {
    return $this->getBy(["username" => $username]);
  }
  
  /**
   * @param string $publicname
   * @return User|NULL
   */
  function getByPublicname(string $publicname): ?User {
    return $this->getBy(["publicname" => $publicname]);
  }
  
  /**
   * @param string $email
   * @return User|NULL
   */
  function getByEmail(string $email): ?User {
    return $this->getBy(["email" => $email]);
  }
  
  /**
   * @param Group|Int $group
   * @return ICollection|User[]
   */
  function findByGroup($group): ICollection {
    return $this->findBy(["group" => $group]);
  }
  
  /**
   * @param Monastery|Int $monastery
   * @return ICollection|User[]
   */
  function findByMonastery($monastery): ICollection {
    return $this->findBy(["monastery" => $monastery]);
  }
  
  /**
   * Get mayor of a town
   * 
   * @param int $town
   * @return User|NULL
   */
  function getTownMayor(int $town): ?User {
    return $this->getBy(["town" => $town, "this->group->level" => 345]);
  }
  
  /**
   * Get citizens of specified town
   * 
   * @param int $town
   * @return ICollection|User[]
   */
  function findTownCitizens(int $town): ICollection {
    return $this->findBy([
      "town" => $town,
      "this->group->level" => [100, 300]
    ]);
  }
  
  /**
   * Get peasants from specified town
   * 
   * @param int $town
   * @return ICollection|User[]
   */
  function findTownPeasants(int $town): ICollection {
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
  function findInGuild(): ICollection {
    return $this->findBy([
      "guild!=" => NULL,
      "this->group->path" => Group::PATH_CITY
    ]);
  }
  
  /**
   * Get users in order
   * 
   * @return ICollection|User[]
   */
  function findInOrder(): ICollection {
    return $this->findBy([
      "order!=" => NULL,
      "this->group->path" => Group::PATH_TOWER
    ]);
  }
  
  /**
   * Get members of specified order
   * 
   * @param int $order
   * @return ICollection|User[]
   */
  function findByOrder(int $order): ICollection {
    return $this->findBy(["order" => $order])
      ->orderBy("orderRank", ICollection::DESC);
  }
  
  /**
   * Get members of specified guild
   * 
   * @param int $guild
   * @return ICollection|User[]
   */
  function findByGuild(int $guild): ICollection {
    return $this->findBy(["guild" => $guild])
      ->orderBy("guildRank", ICollection::DESC);
  }
}
?>