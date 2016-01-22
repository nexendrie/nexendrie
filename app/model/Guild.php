<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Guild as GuildEntity;

/**
 * Guild Model
 *
 * @author Jakub Konečný
 */
class Guild extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $foundingPrice;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->foundingPrice = 1000;
  }
  
  /**
   * Get list of guild from specified town
   * 
   * @param int $town
   * @return GuildEntity[]
   */
  function listOfGuilds($town = 0) {
    if($town === 0) return $this->orm->guilds->findAll();
    else return $this->orm->guilds->findByTown($town);
  }
  
  /**
   * Get specified guild
   * 
   * @param int $id
   * @return GuildEntity|NULL
   */
  function getGuild($id) {
    return $this->orm->guilds->getById($id);
  }
  
  /**
   * Get specified user's guild
   * 
   * @param int $uid
   * @return GuildEntity|NULL
   */
  function getUserGuild($uid = 0) {
    if($uid === 0) $uid = $this->user->id;
    $user = $this->orm->users->getById($uid);
    return $user->guild;
  }
  
  /**
   * Check whetever the user can found a guild
   * 
   * @return bool
   */
  function canFound() {
    if(!$this->user->isLoggedIn()) return false;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != "city") return false;
    elseif($user->guild) return false;
    else return true;
  }
  
  /**
   * Found a guild
   * 
   * @param array $data
   * @return void
   * @throws CannotFoundGuildException
   * @throws GuildNameInUseException
   * @throws InsufficientFundsException
   */
  function found(array $data) {
    if(!$this->canFound()) throw new CannotFoundGuildException;
    $user = $this->orm->users->getById($this->user->id);
    if($this->orm->guilds->getByName($data["name"])) throw new GuildNameInUseException;
    if($user->money < $this->foundingPrice) throw new InsufficientFundsException;
    $guild = new GuildEntity;
    $this->orm->guilds->attach($guild);
    $guild->name = $data["name"];
    $guild->description = $data["description"];
    $guild->town = $this->user->identity->town;
    $guild->founded = time();
    $user->money -= $this->foundingPrice;
    $guild->money = $this->foundingPrice;
    $user->guild = $guild;
    $this->orm->users->persistAndFlush($user);
  }
}

class CannotFoundGuildException extends AccessDeniedException {
  
}

class GuildNameInUseException extends NameInUseException {
  
}

?>