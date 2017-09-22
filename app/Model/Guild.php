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
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->user = $user;
    $this->foundingPrice = $sr->settings["fees"]["foundGuild"];
  }
  
  public function getFoundingPrice(): int {
    return $this->foundingPrice;
  }
  
  /**
   * Get list of guild from specified town
   *
   * @return GuildEntity[]|ICollection
   */
  public function listOfGuilds(int $town = 0): ICollection {
    if($town === 0) {
      return $this->orm->guilds->findAll();
    }
    return $this->orm->guilds->findByTown($town);
  }
  
  /**
   * Get specified guild
   *
   * @throws GuildNotFoundException
   */
  public function getGuild(int $id): GuildEntity {
    $guild = $this->orm->guilds->getById($id);
    if(is_null($guild)) {
      throw new GuildNotFoundException();
    }
    return $guild;
  }
  
  /**
   * Check whether a name can be used
   */
  private function checkNameAvailability(string $name, int $id = NULL): bool {
    $guild = $this->orm->guilds->getByName($name);
    if(is_null($guild)) {
      return true;
    }
    return ($guild->id === $id);
  }
  
  /**
   * Edit specified guild
   *
   * @throws GuildNotFoundException
   * @throws GuildNameInUseException
   */
  public function editGuild(int $id, array $data): void {
    try {
      $guild = $this->getGuild($id);
    } catch(GuildNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      if($key === "name" AND !$this->checkNameAvailability($value, $id)) {
        throw new GuildNameInUseException();
      }
      $guild->$key = $value;
    }
    $this->orm->guilds->persistAndFlush($guild);
  }
  
  /**
   * Get specified user's guild
   */
  public function getUserGuild(int $uid = NULL): ?GuildEntity {
    $user = $this->orm->users->getById($uid ?? $this->user->id);
    if(is_null($user)) {
      return NULL;
    }
    return $user->guild;
  }
  
  /**
   * Check whether the user can found a guild
   */
  public function canFound(): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != GroupEntity::PATH_CITY) {
      return false;
    } elseif($user->guild) {
      return false;
    }
    return true;
  }
  
  /**
   * Found a guild
   *
   * @throws CannotFoundGuildException
   * @throws GuildNameInUseException
   * @throws InsufficientFundsException
   */
  public function found(array $data): void {
    if(!$this->canFound()) {
      throw new CannotFoundGuildException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(!$this->checkNameAvailability($data["name"])) {
      throw new GuildNameInUseException();
    }
    if($user->money < $this->foundingPrice) {
      throw new InsufficientFundsException();
    }
    $guild = new GuildEntity();
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
  
  public function calculateGuildIncomeBonus(int $baseIncome, UserJobEntity $job): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $bonus = $increase = 0;
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($job->user->id);
    if($user->guild AND $user->group->path === GroupEntity::PATH_CITY) {
      $use = false;
      if($job->job->neededSkill->id === $user->guild->skill->id) {
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
   * Check whether the user can join a guild
   */
  public function canJoin(): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path === GroupEntity::PATH_CITY AND !$user->guild) {
      return true;
    }
    return false;
  }
  
  /**
   * Join a guild
   *
   * @throws AuthenticationNeededException
   * @throws CannotJoinGuildException
   * @throws GuildNotFoundException
   */
  public function join(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canJoin()) {
      throw new CannotJoinGuildException();
    }
    try {
      $guild = $this->getGuild($id);
    } catch(GuildNotFoundException $e) {
      throw $e;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->guild = $guild;
    $user->guildRank = 1;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whether the user can leave guild
   * @throws AuthenticationNeededException
   */
  public function canLeave(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild)) {
      return false;
    }
    return !($user->guildRank->id === $this->getMaxRank());
  }
  
  /**
   * Leave guild
   *
   * @throws AuthenticationNeededException
   * @throws CannotLeaveGuildException
   */
  public function leave(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canLeave()) {
      throw new CannotLeaveGuildException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->guild = $user->guildRank = NULL;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whether the user can manage guild
   *
   * @throws AuthenticationNeededException
   */
  public function canManage(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild)) {
      return false;
    }
    return ($user->guildRank->id === $this->getMaxRank());
  }
  
  /**
   * Check whether the user can upgrade guild
   *
   * @throws AuthenticationNeededException
   */
  public function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild)) {
      return false;
    } elseif($user->guildRank->id != $this->getMaxRank()) {
      return false;
    } elseif($user->guild->level >= GuildEntity::MAX_LEVEL) {
      return false;
    }
    return true;
  }
  
  /**
   * Upgrade guild
   *
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeGuildException
   * @throws InsufficientFundsException
   */
  public function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canUpgrade()) {
      throw new CannotUpgradeGuildException();
    }
    /** @var GuildEntity $guild */
    $guild = $this->getUserGuild();
    if($guild->money < $guild->upgradePrice) {
      throw new InsufficientFundsException();
    }
    $guild->money -= $guild->upgradePrice;
    $guild->level++;
    $this->orm->guilds->persistAndFlush($guild);
  }
  
  /**
   * Get members of specified order
   *
   * @return UserEntity[]|ICollection
   */
  public function getMembers(int $guild): ICollection {
    return $this->orm->users->findByGuild($guild);
  }
  
  public function getMaxRank(): int {
    static $rank = NULL;
    if(is_null($rank)) {
      $rank = $this->orm->guildRanks->findAll()->countStored();
    }
    return $rank;
  }
  
  /**
   * Promote a user
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourGuildException
   * @throws CannotPromoteMemberException
   */
  public function promote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canManage()) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if(is_null($user)) {
      throw new UserNotFoundException();
    }
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild) OR $user->guild->id != $admin->guild->id) {
      throw new UserNotInYourGuildException();
    } elseif($user->guildRank->id >= $this->maxRank - 1) {
      throw new CannotPromoteMemberException();
    }
    $user->guildRank = $this->orm->guildRanks->getById($user->guildRank->id + 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Demote a user
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourGuildException
   * @throws CannotDemoteMemberException
   */
  public function demote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canManage()) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if(is_null($user)) {
      throw new UserNotFoundException();
    }
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild) OR $user->guild->id != $admin->guild->id) {
      throw new UserNotInYourGuildException();
    } elseif($user->guildRank->id < 2 OR $user->guildRank->id === $this->maxRank) {
      throw new CannotDemoteMemberException();
    }
    $user->guildRank = $this->orm->guildRanks->getById($user->guildRank->id - 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Kick a user
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourGuildException
   * @throws CannotKickMemberException
   */
  public function kick(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canManage()) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if(is_null($user)) {
      throw new UserNotFoundException();
    }
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if(is_null($user->guild) OR $user->guild->id != $admin->guild->id) {
      throw new UserNotInYourGuildException();
    } elseif($user->guildRank->id === $this->maxRank) {
      throw new CannotKickMemberException();
    }
    $user->guild = $user->guildRank = NULL;
    $this->orm->users->persistAndFlush($user);
  }
}
?>