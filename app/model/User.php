<?php
namespace Nexendrie;

/**
 * User Model
 *
 * @author Jakub Konečný
 */
class User extends \Nette\Object {
  /** @var \Nette\Database\Context Database context */
  protected $db;
  /** Exception error code */
  const REG_DUPLICATE_USERNAME = 1,
    REG_DUPLICATE_EMAIL = 2;
  
  function __construct(\Nette\Database\Context $db) {
    $this->db = $db;
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