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
   * @return GuildEntity
   * @throws GuildNotFoundException
   */
  function getGuild($id) {
    $guild = $this->orm->guilds->getById($id);
    if(!$guild) throw new GuildNotFoundException;
    else return $guild;
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
    $user->guildRank = 4;
    $this->orm->users->persistAndFlush($user);
  }
  
  function calculateRankIncomeBonus($baseIncome) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $bonus = 0;
    $user = $this->orm->users->getById($this->user->id);
    if($user->guild AND $user->group->path === "city") {
      $increase = $user->guildRank->incomeBonus;
      $bonus += (int) $baseIncome /100 * $increase;
    }
    return $bonus;
  }
}

class GuildNotFoundException extends RecordNotFoundException {
  
}

class CannotFoundGuildException extends AccessDeniedException {
  
}

class GuildNameInUseException extends NameInUseException {
  
}

?>