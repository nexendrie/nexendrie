<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Guild as GuildEntity,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\UserJob as UserJobEntity,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Guild Model
 *
 * @author Jakub Konečný
 * @property int $foundingPrice
 * @property-read int $maxRank
 */
class Guild {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $foundingPrice;
  
  use \Nette\SmartObject;
  
  function __construct($foundingPrice, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->foundingPrice = $foundingPrice;
  }
  
  /**
   * return int
   */
  function getFoundingPrice() {
    return $this->foundingPrice;
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
   * Check whetever a name can be used
   * 
   * @param string $name
   * @param int|NULL $id
   * @return bool
   */
  private function checkNameAvailability($name, $id = NULL) {
    $guild = $this->orm->castles->getByName($name);
    if($guild AND $guild->id != $id) return false;
    else return true;
  }
  
  /**
   * Edit specified guild
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws GuildNotFoundException
   * @throws GuildNameInUseException
   */
  function editGuild($id, array $data) {
    try {
      $guild = $this->getGuild($id);
    } catch(GuildNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      if($key === "name" AND !$this->checkNameAvailability($value, $id)) throw new GuildNameInUseException;
      $guild->$key = $value;
    }
    $this->orm->guilds->persistAndFlush($guild);
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
    if($user->group->path != GroupEntity::PATH_CITY) return false;
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
    if(!$this->checkNameAvailability($data["name"])) throw new GuildNameInUseException;
    if($user->money < $this->foundingPrice) throw new InsufficientFundsException;
    $guild = new GuildEntity;
    $this->orm->guilds->attach($guild);
    $guild->name = $data["name"];
    $guild->description = $data["description"];
    $guild->town = $this->user->identity->town;
    $user->lastActive = time();
    $user->money -= $this->foundingPrice;
    $user->guild = $guild;
    $user->guildRank = 4;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * @param int $baseIncome
   * @param UserJobEntity $job
   * @return int
   */
  function calculateGuildIncomeBonus($baseIncome, UserJobEntity $job) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $bonus = $increase = 0;
    $user = $this->orm->users->getById($job->user->id);
    if($user->guild AND $user->group->path === GroupEntity::PATH_CITY) {
      $use = false;
      if($user->guild->skill === NULL) $use = true;
      elseif($job->job->neededSkill->id === $user->guild->skill->id) $use = true;
      if($use) $increase += $user->guildRank->incomeBonus + $user->guild->level - 1;
    }
    $bonus += (int) $baseIncome /100 * $increase;
    return $bonus;
  }
  
  /**
   * Check whetever the user can join a guild
   * 
   * @return bool
   */
  function canJoin() {
    if(!$this->user->isLoggedIn()) return false;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path === GroupEntity::PATH_CITY AND !$user->guild) return true;
    else return false;
  }
  
  /**
   * Join a guild
   * 
   * @param int $id
   * @throws AuthenticationNeededException
   * @throws CannotJoinGuildException
   * @throws GuildNotFoundException
   */
  function join($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canJoin()) throw new CannotJoinGuildException;
    try {
      $guild = $this->getGuild($id);
    } catch(GuildNotFoundException $e) {
      throw $e;
    }
    $user = $this->orm->users->getById($this->user->id);
    $user->guild = $guild;
    $user->guildRank = 1;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whetever the user can leave guild
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canLeave( ) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->guild) return false;
    else return !($user->guildRank->id === 4);
  }
  
  /**
   * Leave guild
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotLeaveGuildException
   */
  function leave() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canLeave()) throw new CannotLeaveGuildException;
    $user = $this->orm->users->getById($this->user->id);
    $user->guild = $user->guildRank = NULL;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whetever the user can manage guild
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canManage() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->guild) return false;
    else return ($user->guildRank->id === 4);
  }
  
  /**
   * Check whetever the user can upgrade guild
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->guild) return false;
    elseif($user->guildRank->id != 4) return false;
    elseif($user->guild->level >= GuildEntity::MAX_LEVEL) return false;
    else return true;
  }
  
  /**
   * Upgrade guild
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeGuildException
   * @throws InsufficientFundsException
   */
  function upgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canUpgrade()) throw new CannotUpgradeGuildException;
    $guild = $this->getUserGuild();
    if($guild->money < $guild->upgradePrice) throw new InsufficientFundsException;
    $guild->money -= $guild->upgradePrice;
    $guild->level++;
    $this->orm->guilds->persistAndFlush($guild);
  }
  
  /**
   * Get members of specified order
   * 
   * @param int $guild
   * @return UserEntity[]
   */
  function getMembers($guild) {
    return $this->orm->users->findByGuild($guild);
  }
  
  /**
   * @return int
   */
  function getMaxRank() {
    static $rank = NULL;
    if($rank === NULL) $rank = $this->orm->guildRanks->findAll()->countStored();
    return $rank;
  }
  
  /**
   * Promote a user
   * 
   * @param int $userId User's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourGuildException
   * @throws CannotPromoteMemberException
   */
  function promote($userId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canManage()) throw new MissingPermissionsException;
    $user = $this->orm->users->getById($userId);
    if(!$user) throw new UserNotFoundException;
    $admin = $this->orm->users->getById($this->user->id);
    if($user->guild->id != $admin->guild->id) throw new UserNotInYourGuildException;
    elseif($user->guildRank->id >= $this->maxRank - 1) throw new CannotPromoteMemberException;
    $user->guildRank = $this->orm->guildRanks->getById($user->guildRank->id + 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Demote a user
   * 
   * @param int $userId User's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourGuildException
   * @throws CannotDemoteMemberException
   */
  function demote($userId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canManage()) throw new MissingPermissionsException;
    $user = $this->orm->users->getById($userId);
    if(!$user) throw new UserNotFoundException;
    $admin = $this->orm->users->getById($this->user->id);
    if($user->guild->id != $admin->guild->id) throw new UserNotInYourGuildException;
    elseif($user->guildRank->id < 2 OR $user->guildRank->id === $this->maxRank) throw new CannotDemoteMemberException;
    $user->guildRank = $this->orm->guildRanks->getById($user->guildRank->id - 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Kick a user
   * 
   * @param int $userId User's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourGuildException
   * @throws CannotKickMemberException
   */
  function kick($userId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canManage()) throw new MissingPermissionsException;
    $user = $this->orm->users->getById($userId);
    if(!$user) throw new UserNotFoundException;
    $admin = $this->orm->users->getById($this->user->id);
    if($user->guild->id != $admin->guild->id) throw new UserNotInYourGuildException;
    elseif($user->guildRank->id === $this->maxRank) throw new CannotKickMemberException;
    $user->guild = $user->guildRank = NULL;
    $this->orm->users->persistAndFlush($user);
  }
}
?>