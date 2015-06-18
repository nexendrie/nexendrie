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
  /** @var \Nexendrie\Permissions */
  protected $permissionsModel;
  
  /**
   * @param \Nette\Database\Context $database Database context
   */
  function __construct(\Nette\Database\Context $database, \Nexendrie\Permissions $permissionsModel) {
    $this->db = $database;
    $this->permissionsModel = $permissionsModel;
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
    $group = $this->permissionsModel->getGroup($row->group);
    $data = array(
      "name" => $row->publicname, "group" => $row->group
    );
    return new NS\Identity($row->id, $group->single_name, $data);
  }
}
?>