<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Security\IAuthenticator;
use Nette\Security\User;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\User as UserEntity;
use Nette\Security\AuthenticationException;

/**
 * Authenticator
 *
 * @author Jakub Konečný
 * @property-write User $user
 */
final class Authenticator implements IAuthenticator {
  protected ORM $orm;
  protected User $user;
  protected Passwords $passwords;
  protected array $roles;
  
  use \Nette\SmartObject;
  
  public function __construct(ORM $orm, SettingsRepository $sr, Passwords $passwords) {
    $this->orm = $orm;
    $this->passwords = $passwords;
    $this->roles = $sr->settings["roles"];
  }
  
  protected function setUser(User $user): void {
    $this->user = $user;
  }
  
  /**
   * Get user's identity
   *
   * @internal
   */
  public function getIdentity(UserEntity $user): Identity {
    $roles = [];
    if($user->banned) {
      /** @var \Nexendrie\Orm\Group $group */
      $group = $this->orm->groups->getById($this->roles["bannedRole"]);
      $roles[0] = $group->singleName;
    } else {
      $roles[0] = $user->group->singleName;
    }
    if($user->guildRank !== null && $user->group->path === \Nexendrie\Orm\Group::PATH_CITY) {
      $roles[1] = AuthorizatorFactory::GUILD_RANK_ROLE_PREFIX . "^" . $user->guildRank->name;
    } elseif($user->orderRank !== null && $user->group->path === \Nexendrie\Orm\Group::PATH_TOWER) {
      $roles[1] = AuthorizatorFactory::ORDER_RANK_ROLE_PREFIX . "^" . $user->orderRank->name;
    }
    $adventure = $this->orm->userAdventures->getUserActiveAdventure($user->id);
    $data = [
      "name" => $user->publicname, "group" => $user->group->id, "notifications" => $user->notifications,
      "level" => $user->group->level, "style" => $user->style, "gender" => $user->gender, "path" => $user->group->path, "town" => $user->town->id, "banned" => $user->banned, "travelling" => !($adventure === null)
    ];
    return new Identity($user->id, $roles, $data);
  }
  
  /**
   * Logins the user
   *
   * @throws AuthenticationException
   */
  public function authenticate(array $credentials): Identity {
    list($email, $password) = $credentials;
    $user = $this->orm->users->getByEmail($email);
    if($user === null) {
      throw new AuthenticationException("E-mail not found.", static::IDENTITY_NOT_FOUND);
    }
    if(!$this->passwords->verify($password, $user->password)) {
      throw new AuthenticationException("Invalid password.", static::INVALID_CREDENTIAL);
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