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
  /** Exception error code */
  const REG_DUPLICATE_USERNAME = 1,
    REG_DUPLICATE_EMAIL = 2;
  
  function __construct(\Nette\Database\Context $database, \Nette\Caching\Cache $cache, \Nexendrie\Group $groupModel) {
    $this->db = $database;
    $this->cache = $cache;
    $this->groupModel = $groupModel;
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
    $group = $this->groupModel->get($row->group);
    $data = array(
      "name" => $row->publicname, "group" => $row->group
    );
    return new NS\Identity($row->id, $group->single_name, $data);
  }
  
  /**
   * Register new user
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @throws RegistrationException
   * @return void
   */
  function register(\Nette\Utils\ArrayHash $data) {
    $username = $this->db->table("users")
       ->where("username", $data["username"]);
    if($username->count() > 0) throw new RegistrationException("Duplicate username.", self::REG_DUPLICATE_USERNAME);
    $email = $this->db->table("users")
       ->where("email", $data["email"]);
    if($email->count() > 0) throw new RegistrationException("Duplicate email.", self::REG_DUPLICATE_EMAIL);
    $data["publicname"] = $data["username"];
    $data["password"] = \Nette\Security\Passwords::hash($data["password"]);
    $this->db->query("INSERT INTO users", $data);
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
?>