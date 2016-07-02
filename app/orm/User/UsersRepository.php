<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method User|NULL getById($id)
 * @method User|NULL getByUsername(string $username)
 * @method User|NULL getByPublicname(string $publicname)
 * @method User|NULL getByEmail(string $email)
 * @method ICollection|User[] findByGroup($group)
 * @method ICollection|User[] findByMonastery($monastery)
 */
class UsersRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [User::class];
  }
  
  /**
   * Get mayor of a town
   * 
   * @param int $town
   * @return User|NULL
   */
  function getTownMayor($town) {
    return $this->getBy(array("town" => $town, "this->group->level" => 345));
  }
  
  /**
   * Get citizens of specified town
   * 
   * @param int $town
   * @return ICollection|User[]
   */
  function findTownCitizens($town) {
    return $this->findBy(array(
      "town" => $town,
      "this->group->level" => array(100, 300)
    ));
  }
  
  /**
   * Get peasants from specified town
   * 
   * @param int $town
   * @return ICollection|User[]
   */
  function findTownPeasants($town) {
    return $this->findBy(array(
      "town" => $town,
      "this->group->level" => array(50)
    ));
  }
  
  /**
   * Get users in guild
   * 
   * @return ICollection|User[]
   */
  function findInGuild() {
    return $this->findBy(array(
      "guild!=" => NULL,
      "this->group->path" => Group::PATH_CITY
    ));
  }
  
  /**
   * Get users in order
   * 
   * @return ICollection|User[]
   */
  function findInOrder() {
    return $this->findBy(array(
      "order!=" => NULL,
      "this->group->path" => Group::PATH_TOWER
    ));
  }
  
  /**
   * Get members of specified order
   * 
   * @param int $order
   * @return ICollection|User[]
   */
  function findByOrder($order) {
    return $this->findBy(array("order" => $order))
      ->orderBy("orderRank", ICollection::DESC);
  }
  
  /**
   * Get members of specified guild
   * 
   * @param int $guild
   * @return ICollection|User[]
   */
  function findByGuild($guild) {
    return $this->findBy(array("guild" => $guild))
      ->orderBy("guildRank", ICollection::DESC);
  }
}
?>