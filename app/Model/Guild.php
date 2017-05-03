<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Guild as GuildEntity,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\UserJob as UserJobEntity,
    Nexendrie\Orm\Group as GroupEntity,
    Nextras\Orm\Collection\ICollection;

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
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->user = $user;
    $this->foundingPrice = $sr->settings["fees"]["foundGuild"];
  }
  
  /**
   * return int
   */
  function getFoundingPrice(): int {
    return $this->foundingPrice;
  }
  
  /**
   * Get list of guild from specified town
   * 
   * @param int $town
   * @return GuildEntity[]|ICollection
   */
  function listOfGuilds(int $town = 0): ICollection {
    if($town === 0) {
      return $this->orm->guilds->findAll();
    }
    else {
      return $this->orm->guilds->findByTown($town);
    }
  }
  
  /**
   * Get specified guild
   * 
   * @param int $id
   * @return GuildEntity
   * @throws GuildNotFoundException
   */
  function getGuild(int $id): GuildEntity {
    $guild = $this->orm->guilds->getById($id);
    if(!$guild) {
      throw new GuildNotFoundException;
    }
    else {
      return $guild;
    }
  }
  
  /**
   * Check whetever a name can be used
   * 
   * @param string $name
   * @param int $id
   * @return bool
   */
  private function checkNameAvailability(string $name, int $id = NULL): bool {
    $guild = $this->orm->castles->getByName($name);
    if($guild AND $guild->id != $id) {
      return false;
    } else {
      return true;
    }
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
  function editGuild(int $id, array $data): void {
    try {
      $guild = $this->getGuild($id);
    } catch(GuildNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      if($key === "name" AND !$this->checkNameAvailability($value, $id)) {
        throw new GuildNameInUseException;
      }
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
  function getUserGuild(int $uid = 0): ?GuildEntity {
    if($uid === 0) {
      $uid = $this->user->id;
    }
    $user = $this->orm->users->getById($uid);
    return $user->guild;
  }
  
  /**
   * Check whetever the user can found a guild
   * 
   * @return bool
   */
  function canFound(): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != GroupEntity::PATH_CITY) {
      return false;
    } elseif($user->guild) {
      return false;
    } else {
      return true;
    }
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
  function found(array $data): void {
    if(!$this->canFound()) {
      throw new CannotFoundGuildException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$this->checkNameAvailability($data["name"])) {
      throw new GuildNameInUseException;
    }
    if($user->money < $this->foundingPrice) {
      throw new InsufficientFundsException;
    }
    $guild = new GuildEntity;
    $this->orm->guilds->attach($guild);
    $guild->name = $data["name"];
    $guild->description = $data["description"];
    $guild->town = $this->user->identity->town;
    $user->lastActive = time();
    $user->money -= $this->foundingPrice;
    $user->guild = $guild;
    $user->guildRank = $this->getMaxRank();
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * @param int $baseIncome
   * @param UserJobEntity $job
   * @return int
   */
  function calculateGuildIncomeBonus(int $baseIncome, UserJobEntity $job): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $bonus = $increase = 0;
    $user = $this->orm->users->getById($job->user->id);
    if($user->guild AND $user->group->path === GroupEntity::PATH_CITY) {
      $use = false;
      if($user->guild->skill === NULL) {
        $use = true;
      } elseif($job->job->neededSkill->id === $user->guild->skill->id) {
        $use = true;
      }
      if($use) {
        $increase += $user->guildRank->incomeBonus + $user->guild->level - 1;
      }
    }
    $bonus += (int) ($baseIncome /100 * $increase);
    return $bonus;
  }
  
  /**
   * Check whetever the user can join a guild
   * 
   * @return bool
   */
  function canJoin(): bool {
    if(!$this->user->isLoggedIn()) return false;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path === GroupEntity::PATH_CITY AND !$user->guild) {
      return true;
    } else {
      return false;
    }
  }
  
  /**
   * Join a guild
   * 
   * @param int $id
   * @throws AuthenticationNeededException
   * @throws CannotJoinGuildException
   * @throws GuildNotFoundException
   */
  function join(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canJoin()) {
      throw new CannotJoinGuildException;
    }
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
  function canLeave(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->guild) {
      return false;
    } else {
      return !($user->guildRank->id === $this->getMaxRank());
    }
  }
  
  /**
   * Leave guild
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotLeaveGuildException
   */
  function leave(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$this->canLeave()) {
      throw new CannotLeaveGuildException;
    }
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
  function canManage(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->guild) {
      return false;
    } else {
      return ($user->guildRank->id === $this->getMaxRank());
    }
  }
  
  /**
   * Check whetever the user can upgrade guild
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->guild) {
      return false;
    } elseif($user->guildRank->id != $this->getMaxRank()) {
      return false;
    } elseif($user->guild->level >= GuildEntity::MAX_LEVEL) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * Upgrade guild
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeGuildException
   * @throws InsufficientFundsException
   */
  function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$this->canUpgrade()) {
      throw new CannotUpgradeGuildException;
    }
    $guild = $this->getUserGuild();
    if($guild->money < $guild->upgradePrice) {
      throw new InsufficientFundsException;
    }
    $guild->money -= $guild->upgradePrice;
    $guild->level++;
    $this->orm->guilds->persistAndFlush($guild);
  }
  
  /**
   * Get members of specified order
   * 
   * @param int $guild
   * @return UserEntity[]|ICollection
   */
  function getMembers(int $guild): ICollection {
    return $this->orm->users->findByGuild($guild);
  }
  
  /**
   * @return int
   */
  function getMaxRank(): int {
    static $rank = NULL;
    if($rank === NULL) {
      $rank = $this->orm->guildRanks->findAll()->countStored();
    }
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
  function promote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canManage()) {
      throw new MissingPermissionsException;
    }
    $user = $this->orm->users->getById($userId);
    if(!$user) {
      throw new UserNotFoundException;
    }
    $admin = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild) OR $user->guild->id != $admin->guild->id) {
      throw new UserNotInYourGuildException;
    } elseif($user->guildRank->id >= $this->maxRank - 1) {
      throw new CannotPromoteMemberException;
    }
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
  function demote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canManage()) {
      throw new MissingPermissionsException;
    }
    $user = $this->orm->users->getById($userId);
    if(!$user) {
      throw new UserNotFoundException;
    }
    $admin = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild) OR $user->guild->id != $admin->guild->id) {
      throw new UserNotInYourGuildException;
    } elseif($user->guildRank->id < 2 OR $user->guildRank->id === $this->maxRank) {
      throw new CannotDemoteMemberException;
    }
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
  function kick(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canManage()) {
      throw new MissingPermissionsException;
    }
    $user = $this->orm->users->getById($userId);
    if(!$user) {
      throw new UserNotFoundException;
    }
    $admin = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild) OR $user->guild->id != $admin->guild->id) {
      throw new UserNotInYourGuildException;
    } elseif($user->guildRank->id === $this->maxRank) {
      throw new CannotKickMemberException;
    }
    $user->guild = $user->guildRank = NULL;
    $this->orm->users->persistAndFlush($user);
  }
}
?>