<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Security\User;
use Nette\Security\Passwords;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\User as UserEntity;
use Nextras\Orm\Collection\ICollection;
use Nette\InvalidArgumentException;

/**
 * User Manager
 *
 * @author Jakub Konečný
 */
final class UserManager {
  protected ORM $orm;
  protected User $user;
  protected Passwords $passwords;
  protected array $roles = [];
  protected array $newUser;
  /** Exception error code */
  public const REG_DUPLICATE_NAME = 1,
    REG_DUPLICATE_EMAIL = 2,
    SET_INVALID_PASSWORD = 3;

  use \Nette\SmartObject;

  public function __construct(ORM $orm, SettingsRepository $sr, User $user, Passwords $passwords) {
    $this->orm = $orm;
    $this->user = $user;
    $this->passwords = $passwords;
    $this->roles = $sr->settings["roles"];
    $this->newUser = $sr->settings["newUser"];
  }

  /**
   * Checks whether a name is available
   *
   * @param int|null $uid Id of user who can use the name
   * @throws InvalidArgumentException
   */
  public function nameAvailable(string $name, int $uid = null): bool {
    $row = $this->orm->users->getByPublicname($name);
    if($row === null) {
      return true;
    } elseif(!is_int($uid)) {
      return false;
    } elseif($row->id === $uid) {
      return true;
    }
    return false;
  }

  /**
   * Checks whether an e-mail is available
   *
   * @param int|null $uid Id of user who can use the e-mail
   * @throws InvalidArgumentException
   */
  public function emailAvailable(string $email, int $uid = null): bool {
    $row = $this->orm->users->getByEmail($email);
    if($row === null) {
      return true;
    } elseif(!is_int($uid)) {
      return false;
    } elseif($row->id === $uid) {
      return true;
    }
    return false;
  }

  /**
   * Register new user
   *
   * @throws RegistrationException
   */
  public function register(array $data): void {
    if(!$this->emailAvailable($data["email"])) {
      throw new RegistrationException("Duplicate email.", static::REG_DUPLICATE_EMAIL);
    }
    if(!$this->nameAvailable($data["publicname"])) {
      throw new RegistrationException("Duplicate name.", static::REG_DUPLICATE_NAME);
    }
    $user = new UserEntity();
    $this->orm->users->attach($user);
    $data += $this->newUser;
    foreach($data as $key => $value) {
      if($key === "password") {
        $value = $this->passwords->hash($data["password"]);
      }
      $user->$key = $value;
    }
    $user->group = $this->roles["loggedInRole"];
    $this->orm->users->persistAndFlush($user);
  }

  /**
   * Get user's settings
   *
   * @throws AuthenticationNeededException
   */
  public function getSettings(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $settings = [
      "publicname" => $user->publicname, "email" => $user->email, "style" => $user->style, "gender" => $user->gender,
      "notifications" => $user->notifications, "api" => $user->api,
    ];
    return $settings;
  }

  /**
   * Change user's settings
   *
   * @throws AuthenticationNeededException
   * @throws SettingsException
   */
  public function changeSettings(array $settings): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    if(!$this->nameAvailable($settings["publicname"], $this->user->id)) {
      throw new SettingsException("The name is used by someone else.", static::REG_DUPLICATE_NAME);
    }
    if(!$this->emailAvailable($settings["email"], $this->user->id)) {
      throw new SettingsException("The e-mail is used by someone else.", static::REG_DUPLICATE_EMAIL);
    }
    $settings = array_filter($settings, function($value) {
      return $value !== null && $value !== "";
    });
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    foreach($settings as $key => $value) {
      switch($key) {
        case "password_new":
          if(!$this->passwords->verify($settings["password_old"], $user->password)) {
            throw new SettingsException("Invalid password.", static::SET_INVALID_PASSWORD);
          }
          $user->password = $this->passwords->hash($value);
          unset($settings[$key], $settings["password_old"], $settings["password_check"]);
          break;
      }
      $skip = ["password_old", "password_new", "password_check"];
      if(!in_array($key, $skip, true)) {
        $user->$key = $value;
      }
    }
    $this->orm->users->persistAndFlush($user);
  }

  /**
   * Get list of all users
   *
   * @return UserEntity[]|ICollection
   */
  public function listOfUsers(): ICollection {
    return $this->orm->users->findAll()->orderBy("group")->orderBy("id");
  }

  /**
   * @throws UserNotFoundException
   */
  public function edit(int $id, array $values): void {
    try {
      $user = $this->get($id);
    } catch(UserNotFoundException $e) {
      throw $e;
    }
    foreach($values as $key => $value) {
      $user->$key = $value;
    }
    $this->orm->users->persistAndFlush($user);
  }

  public function get(int $id): UserEntity {
    $user = $this->orm->users->getById($id);
    if($user === null) {
      throw new UserNotFoundException();
    }
    return $user;
  }
}

?>