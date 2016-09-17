<?php
namespace Nexendrie\Model;

use Nette\Security as NS,
    Nexendrie\Orm\User as UserEntity,
    Nette\InvalidArgumentException;

/**
 * User Manager
 *
 * @author Jakub Konečný
 * @property \Nette\Security\User $user
 */
class UserManager implements NS\IAuthenticator {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var array */
  protected $roles = [];
  /** @var array */
  protected $newUser;
  /** Exception error code */
  const REG_DUPLICATE_USERNAME = 1,
    REG_DUPLICATE_EMAIL = 2,
    SET_INVALID_PASSWORD = 3;
  
  use \Nette\SmartObject;
  
  /**
   * @param array $roles
   * @param array $newUser
   * @param \Nexendrie\Orm\Model $orm
   */
  function __construct(array $roles, array $newUser, \Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
    $this->roles = $roles;
    $this->newUser = $newUser;
  }
  
  /**
   * @param \Nette\Security\User $user
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * Checks whetever a name is available
   * 
   * @param string $name
   * @param string $type username/publicname
   * @param int $uid Id of user who can use the name
   * @return bool
   * @throws InvalidArgumentException
   */
  function nameAvailable($name, $type = "username", $uid = NULL) {
    if(!is_int($uid) AND !is_null($uid)) throw new InvalidArgumentException("Parameter uid for " . __METHOD__ . " must be either integer or null.");
    $types = ["username", "publicname"];
    if(!in_array($type, $types)) throw new InvalidArgumentException("Parameter type for " . __METHOD__ . " must be either \"username\" or \"publicname\".");
    if($type === "username") $method = "getByUsername";
    else $method = "getByPublicname";
    $row = $this->orm->users->$method($name);
    if(!$row) return true;
    elseif(!is_int($uid)) return false;
    elseif($row->id === $uid) return true;
    else return false;
  }
  
  /**
   * Checks whetever an e-mail is available
   * 
   * @param string $email
   * @param int $uid Id of user who can use the e-mail
   * @return bool
   * @throws InvalidArgumentException
   */
  function emailAvailable($email, $uid = NULL) {
    if(!is_int($uid) AND !is_null($uid)) throw new InvalidArgumentException("Parameter uid for " . __METHOD__ . " must be either integer or null.");
    if(!is_int($uid) AND !is_null($uid)) throw new InvalidArgumentException("Parameter uid for " . __METHOD__ . " must be either integer or null.");
    $row = $this->orm->users->getByEmail($email);
    if(!$row) return true;
    elseif(!is_int($uid)) return false;
    elseif($row->id === $uid) return true;
    else return false;
  }
  
  /**
   * Get user's identity
   * 
   * @param \Nexendrie\Orm\User $user
   * @return \Nette\Security\Identity
   */
  protected function getIdentity(UserEntity $user) {
    if($user->banned) {
      $role = $this->orm->groups->getById($this->roles["bannedRole"])->singleName;
      $banned = true;
    } else {
      $role = $user->group->singleName;
      $banned = false;
    }
    $adventure = $this->orm->userAdventures->getUserActiveAdventure($user->id);
    $data = [
      "name" => $user->publicname, "group" => $user->group->id,
      "level" => $user->group->level, "style" => $user->style, "gender" => $user->gender, "path" => $user->group->path, "town" => $user->town->id, "banned" => $banned, "travelling" => !($adventure === NULL)
    ];
    return new NS\Identity($user->id, $role, $data);
  }
  
  /**
   * Logins the user
   * 
   * @param array $credentials
   * @return \Nette\Security\Identity User's identity
   * @throws \Nette\Security\AuthenticationException
   */
  function authenticate(array $credentials) {
    list($username, $password) = $credentials;
    $user = $this->orm->users->getByUsername($username);
    if(!$user) {
      throw new NS\AuthenticationException("User not found.", self::IDENTITY_NOT_FOUND);
    }
    if(!NS\Passwords::verify($password, $user->password)) {
      throw new NS\AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
    }
    $user->lastActive = time();
    $this->orm->users->persistAndFlush($user);
    return $this->getIdentity($user);
  }
  
  /**
   * Refresh user's identity
   * 
   * @return void
   * @throws AuthenticationNeededException
   */
  function refreshIdentity() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    $user = $this->orm->users->getById($this->user->id);
    $this->user->login($this->getIdentity($user));
  }
  
  /**
   * Register new user
   * 
   * @param array $data
   * @throws RegistrationException
   * @return void
   */
  function register(array $data) {
    if(!$this->nameAvailable($data["username"])) throw new RegistrationException("Duplicate username.", self::REG_DUPLICATE_USERNAME);
    if(!$this->emailAvailable($data["email"])) throw new RegistrationException("Duplicate email.", self::REG_DUPLICATE_EMAIL);
    $user = new UserEntity;
    $this->orm->users->attach($user);
    $data += $this->newUser;
    foreach($data as $key => $value) {
      if($key === "password") $value = \Nette\Security\Passwords::hash($data["password"]);
      $user->$key = $value;
    }
    $user->publicname = $data["username"];
    $user->group = $this->roles["loggedInRole"];
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Get user's settings
   * 
   * @return array
   * @throws AuthenticationNeededException
   */
  function getSettings() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
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
   * @param \Nette\Utils\ArrayHash $settings
   * @throws AuthenticationNeededException
   * @throws SettingsException
   * @return void
   */
  function changeSettings(\Nette\Utils\ArrayHash $settings) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    if(!$this->nameAvailable($settings["publicname"], "publicname", $this->user->id)) throw new SettingsException("The public name is used by someone else.", self::REG_DUPLICATE_USERNAME);
    if(!$this->emailAvailable($settings["email"], $this->user->id)) throw new SettingsException("The e-mail is used by someone else.", self::REG_DUPLICATE_EMAIL);
    $user = $this->orm->users->getById($this->user->id);
    foreach($settings as $key => $value) {
      switch($key) {
        case "password_new":
          if(!empty($value)) {
            if(!NS\Passwords::verify($settings["password_old"], $user->password)) {
              throw new SettingsException("Invalid password.", self::SET_INVALID_PASSWORD);
            }
            $user->password = NS\Passwords::hash($value);
          }
          unset($settings[$key], $settings["password_old"], $settings["password_check"]);
  break;
      }
      $skip = ["password_old", "password_new", "password_check"];
      if(!in_array($key, $skip)) $user->$key = $value;
    }
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Get list of all users
   * 
   * @return UserEntity[]
   */
  function listOfUsers() {
    return $this->orm->users->findAll()->orderBy("group")->orderBy("id");
  }
  
  /**
   * @param int $id User's id
   * @param \Nette\Utils\ArrayHash $values
   * @return void
   */
  function edit($id, \Nette\Utils\ArrayHash $values) {
    $user = $this->orm->users->getById($id);
    foreach($values as $key => $value) {
      $user->$key = $value;
    }
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * @param int $id
   * @return UserEntity
   * @throws UserNotFoundException
   */
  function get($id) {
    $user = $this->orm->users->getById($id);
    if(!$user) throw new UserNotFoundException;
    else return $user;
  }
}
?>