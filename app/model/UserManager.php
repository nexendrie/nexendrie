<?php
namespace Nexendrie;

use Nette\Security as NS;

/**
 * User Model
 *
 * @author Jakub Konečný
 */
class UserManager extends \Nette\Object implements NS\IAuthenticator {
  /** @var \Nette\Database\Context Database context */
  protected $db;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nexendrie\Group */
  protected $groupModel;
  /** @var \Nette\Security\User */
  protected $user;
  /** Exception error code */
  const REG_DUPLICATE_USERNAME = 1,
    REG_DUPLICATE_EMAIL = 2,
    SET_INVALID_PASSWORD = 3;
  
  /**
   * @param \Nette\Database\Context $database
   * @param \Nette\Caching\Cache $cache
   * @param \Nexendrie\Group $groupModel
   */
  function __construct(\Nette\Database\Context $database, \Nette\Caching\Cache $cache, \Nexendrie\Group $groupModel) {
    $this->db = $database;
    $this->cache = $cache;
    $this->groupModel = $groupModel;
  }
  
  /**
   * @param \Nette\Security\User $user
   * @return void
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
   * @throws \Nette\InvalidArgumentException
   */
  function nameAvailable($name, $type = "username", $uid = NULL) {
    if(!is_int($uid) AND !is_null($uid)) throw new \Nette\InvalidArgumentException("Parameter uid for " . __METHOD__ . " must be either integer or null.");
    $types = array("username", "publicname");
    if(!in_array($type, $types)) throw new \Nette\InvalidArgumentException("Parameter type for " . __METHOD__ . " must be either \"username\" or \"publicname\".");
    $result = $this->db->table("users")
      ->where($type, $name);
    if(is_int($uid)) $result->where("NOT id", $uid);
    return !($result->count() > 0);
  }
  
  /**
   * Checks whetever an e-mail is available
   * 
   * @param string $email
   * @param int $uid Id of user who can use the e-mail
   * @return bool
   */
  function emailAvailable($email, $uid = NULL) {
    if(!is_int($uid) AND !is_null($uid)) throw new \Nette\InvalidArgumentException("Parameter uid for " . __METHOD__ . " must be either integer or null.");
    $result = $this->db->table("users")
      ->where("email", $email);
    if(is_int($uid)) $result->where("NOT id", $uid);
    return !($result->count() > 0);
  }
  
  /**
   * Logins the user
   * 
   * @param array $credentials
   * @return \Nette\Security\Identity User's identity
   * @throws NS\AuthenticationException
   */
  function authenticate(array $credentials) {
    list($username, $password) = $credentials;
    $row = $this->db->table("users")
      ->where("username", $username)->fetch();
    if(!$row) {
      throw new NS\AuthenticationException("User not found.", NS\IAuthenticator::IDENTITY_NOT_FOUND);
    }
    if(!NS\Passwords::verify($password, $row->password)) {
      throw new NS\AuthenticationException("Invalid password.", NS\IAuthenticator::INVALID_CREDENTIAL);
    }
    if($row->banned) {
      $group = $this->groupModel->get(BANNED_ROLE);
    } else {
      $group = $this->groupModel->get($row->group);
    }
    $role = $group->single_name;
    $data = array(
      "name" => $row->publicname, "group" => $row->group, "style" => $row->style
    );
    return new NS\Identity($row->id, $role, $data);
  }
  
  /**
   * Register new user
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @throws RegistrationException
   * @return void
   */
  function register(\Nette\Utils\ArrayHash $data) {
    if(!$this->nameAvailable($data["username"])) throw new RegistrationException("Duplicate username.", self::REG_DUPLICATE_USERNAME);
    if(!$this->emailAvailable($data["email"])) throw new RegistrationException("Duplicate email.", self::REG_DUPLICATE_EMAIL);
    $data["publicname"] = $data["username"];
    $data["password"] = \Nette\Security\Passwords::hash($data["password"]);
    $data["joined"] = time();
    $this->db->query("INSERT INTO users", $data);
    $this->cache->remove("users_names");
  }
  
  /**
   * Get user's settings
   * 
   * @return \stdClass
   * @throws \Nette\Application\ForbiddenRequestException
   */
  function getSettings() {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    $user = $this->db->table("users")->get($this->user->id);
    $settings = array(
      "publicname" => $user->publicname, "email" => $user->email, "infomails" => (bool) $user->infomails,
      "style" => $user->style
    );
    return $settings;
  }
  
  /**
   * Change user's settings
   * 
   * @param \Nette\Utils\ArrayHash $settings
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws SettingsException
   * @return void
   */
  function changeSettings(\Nette\Utils\ArrayHash $settings) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->nameAvailable($settings["publicname"], "publicname", $this->user->id)) throw new SettingsException("The public name is used by someone else.", self::REG_DUPLICATE_USERNAME);
    if(!$this->emailAvailable($settings["email"], $this->user->id)) throw new SettingsException("The e-mail is used by someone else.", self::REG_DUPLICATE_EMAIL);
    foreach($settings as $key => $value) {
      switch($key) {
case "infomails":
  $value = (int) $value;
  break;
case "password_new":
  if(!empty($value)) {
    $user = $this->db->table("users")->get($this->user->id);
    if(!NS\Passwords::verify($settings["password_old"], $user->password)) {
      throw new SettingsException("Invalid password.", self::SET_INVALID_PASSWORD);
    }
    $settings["password"] = \Nette\Security\Passwords::hash($value);
  }
  unset($settings[$key], $settings["password_old"], $settings["password_check"]);
  break;
      }
    }
    $this->db->query("UPDATE users SET ? WHERE id=?", $settings, $this->user->id);
    $this->cache->remove("users_names");
  }
}

/**
 * Registration exception
 * 
 * @author Jakub Konečný
 */
class RegistrationException extends \Exception {
  
}

/**
 * Settings exception
 * 
 * @author Jakub Konečný
 */
class SettingsException extends \Exception {
  
}
?>