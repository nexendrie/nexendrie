<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Security\User,
    Nette\Security\Passwords,
    Nexendrie\Orm\Model as ORM,
    Nexendrie\Orm\User as UserEntity,
    Nextras\Orm\Collection\ICollection,
    Nette\InvalidArgumentException;

/**
 * User Manager
 *
 * @author Jakub Konečný
 * @property-write User $user
 */
class UserManager {
  /** @var ORM */
  protected $orm;
  /** @var User */
  protected $user;
  /** @var array */
  protected $roles = [];
  /** @var array */
  protected $newUser;
  /** Exception error code */
  public const REG_DUPLICATE_USERNAME = 1,
    REG_DUPLICATE_EMAIL = 2,
    SET_INVALID_PASSWORD = 3;
  
  use \Nette\SmartObject;
  
  public function __construct(ORM $orm, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->roles = $sr->settings["roles"];
    $this->newUser = $sr->settings["newUser"];
  }
  
  public function setUser(User $user) {
    $this->user = $user;
  }
  
  /**
   * Checks whether a name is available
   *
   * @param string $type username/publicname
   * @param int|NULL $uid Id of user who can use the name
   * @throws InvalidArgumentException
   */
  public function nameAvailable(string $name, string $type = "username", int $uid = NULL): bool {
    $types = ["username", "publicname"];
    if(!in_array($type, $types)) {
      throw new InvalidArgumentException("Parameter type for " . __METHOD__ . " must be either \"username\" or \"publicname\".");
    }
    $method = ($type === "username") ? "getByUsername" : "getByPublicname";
    $row = $this->orm->users->$method($name);
    if(is_null($row)) {
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
   * @param int|NULL $uid Id of user who can use the e-mail
   * @throws InvalidArgumentException
   */
  public function emailAvailable(string $email, int $uid = NULL): bool {
    $row = $this->orm->users->getByEmail($email);
    if(is_null($row)) {
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
    if(!$this->nameAvailable($data["username"])) {
      throw new RegistrationException("Duplicate username.", self::REG_DUPLICATE_USERNAME);
    }
    if(!$this->emailAvailable($data["email"])) {
      throw new RegistrationException("Duplicate email.", self::REG_DUPLICATE_EMAIL);
    }
    $user = new UserEntity();
    $this->orm->users->attach($user);
    $data += $this->newUser;
    foreach($data as $key => $value) {
      if($key === "password") {
        $value = Passwords::hash($data["password"]);
      }
      $user->$key = $value;
    }
    $user->publicname = $data["username"];
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
      "publicname" => $user->publicname, "email" => $user->email, "infomails" =>  $user->infomails,
      "style" => $user->style, "gender" => $user->gender
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
    if(!$this->nameAvailable($settings["publicname"], "publicname", $this->user->id)) {
      throw new SettingsException("The public name is used by someone else.", self::REG_DUPLICATE_USERNAME);
    }
    if(!$this->emailAvailable($settings["email"], $this->user->id)) {
      throw new SettingsException("The e-mail is used by someone else.", self::REG_DUPLICATE_EMAIL);
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    foreach($settings as $key => $value) {
      switch($key) {
        case "password_new":
          if(!empty($value)) {
            if(!Passwords::verify($settings["password_old"], $user->password)) {
              throw new SettingsException("Invalid password.", self::SET_INVALID_PASSWORD);
            }
            $user->password = Passwords::hash($value);
          }
          unset($settings[$key], $settings["password_old"], $settings["password_check"]);
  break;
      }
      $skip = ["password_old", "password_new", "password_check"];
      if(!in_array($key, $skip)) {
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
    if(is_null($user)) {
      throw new UserNotFoundException();
    }
    return $user;
  }
}
?>