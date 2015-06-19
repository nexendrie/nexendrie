<?php
namespace Nexendrie;

/**
 * Profile Model
 *
 * @author Jakub Konečný
 */
class Profile extends \Nette\Object {
  /** @var \Nette\Database\Context Database context */
  protected $db;
  /** @var \Nexendrie\Permissions */
  protected $permissionsModel;
  
  function __construct(\Nette\Database\Context $database, \Nexendrie\Permissions $permissionsModel) {
    $this->db = $database;
    $this->permissionsModel = $permissionsModel;
  }
  
  /**
   * Show user's profile
   * 
   * @param string $username
   * @return boolean|\stdClass
   */
  function view($username) {
    $result = $this->db->table("users")
      ->where("username", $username);
    if($result->count() === 0) return false;
    $user = $result->fetch();
    $return = new \stdClass;
    $return->name = $user->publicname;
    $return->joined = $user->joined;
    $group = $this->permissionsModel->getGroup($user->group);
    if(!$group) $return->title = "";
    else $return->title = $group->single_name;
    return $return;
  }
}
?>