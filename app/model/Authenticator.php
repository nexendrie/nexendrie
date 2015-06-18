<?php
namespace Nexendrie;

use Nette\Security as NS;

/**
 * Authenticator
 *
 * @author Jakub Konečný
 */
class Authenticator extends \Nette\Object implements NS\IAuthenticator {
  /** @var \Nette\Database\Context Database context */
  protected $db;
  
  /**
   * @param \Nette\Database\Context $database Database context
   */
  function __construct(\Nette\Database\Context $database) {
    $this->db = $database;
  }
  
  /**
   * Logins the user
   * 
   * @param array $credentials
   * @return \Nette\Security\Identity User"s identity
   */
  function authenticate(array $credentials) {
    list($username, $password) = $credentials;
    $row = $this->db->table("users")
      ->where("username", $username)->fetch();
    if(!$row) {
      throw new NS\AuthenticationException("User not found.");
    }
    if(!NS\Passwords::verify($password, $row->password)) {
      throw new NS\AuthenticationException("Invalid password.");
    }
    $group = $this->db->table("groups")->get($row->group);
    $data = array(
      "name" => $row->publicname, "group" => $row->group
    );
    return new NS\Identity($row->id, $group->single_name, $data);
  }
}
?>