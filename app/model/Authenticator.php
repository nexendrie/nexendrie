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
  /** @var \Nexendrie\Group */
  protected $groupModel;
  
  /**
   * @param \Nette\Database\Context $database Database context
   */
  function __construct(\Nette\Database\Context $database, \Nexendrie\Group $groupModel) {
    $this->db = $database;
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
}
?>