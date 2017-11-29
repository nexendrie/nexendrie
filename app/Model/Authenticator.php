<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Security\IAuthenticator,
    Nette\Security\User,
    Nette\Security\Identity,
    Nette\Security\Passwords,
    Nexendrie\Orm\Model as ORM,
    Nexendrie\Orm\User as UserEntity,
    Nette\Security\AuthenticationException;

/**
 * Authenticator
 *
 * @author Jakub Konečný
 * @property-write User $user
 */
class Authenticator implements IAuthenticator {
  /** @var ORM */
  protected $orm;
  /** @var User */
  protected $user;
  /** @var array */
  protected $roles;
  
  use \Nette\SmartObject;
  
  public function __construct(ORM $orm, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->roles = $sr->settings["roles"];
  }
  
  public function setUser(User $user) {
    $this->user = $user;
  }
  
  /**
   * Get user's identity
   */
  protected function getIdentity(UserEntity $user): Identity {
    $roles = [];
    if($user->banned) {
      /** @var \Nexendrie\Orm\Group $group */
      $group = $this->orm->groups->getById($this->roles["bannedRole"]);
      $roles[0] = $group->singleName;
    } else {
      $roles[0] = $user->group->singleName;
    }
    if(!is_null($user->guildRank) AND $user->group->path === \Nexendrie\Orm\Group::PATH_CITY) {
      $roles[1] = AuthorizatorFactory::GUILD_RANK_ROLE_PREFIX . "^" . $user->guildRank->name;
    } elseif(!is_null($user->orderRank) AND $user->group->path === \Nexendrie\Orm\Group::PATH_TOWER) {
      $roles[1] = AuthorizatorFactory::ORDER_RANK_ROLE_PREFIX . "^" . $user->orderRank->name;
    }
    $adventure = $this->orm->userAdventures->getUserActiveAdventure($user->id);
    $data = [
      "name" => $user->publicname, "group" => $user->group->id,
      "level" => $user->group->level, "style" => $user->style, "gender" => $user->gender, "path" => $user->group->path, "town" => $user->town->id, "banned" => $user->banned, "travelling" => !(is_null($adventure))
    ];
    return new Identity($user->id, $roles, $data);
  }
  
  /**
   * Logins the user
   *
   * @throws AuthenticationException
   */
  public function authenticate(array $credentials): Identity {
    list($username, $password) = $credentials;
    $user = $this->orm->users->getByUsername($username);
    if(is_null($user)) {
      throw new AuthenticationException("User not found.", self::IDENTITY_NOT_FOUND);
    }
    if(!Passwords::verify($password, $user->password)) {
      throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
    }
    $user->lastActive = time();
    $this->orm->users->persistAndFlush($user);
    return $this->getIdentity($user);
  }
  
  /**
   * Refresh user's identity
   *
   * @throws AuthenticationNeededException
   */
  public function refreshIdentity(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $this->user->login($this->getIdentity($user));
  }
}
?>