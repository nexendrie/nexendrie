<?php
namespace Nexendrie;

use Nette\Utils\Arrays;

/**
 * Profile Model
 *
 * @author Jakub Konečný
 */
class Profile extends \Nette\Object {
  /** @var \Nette\Database\Context Database context */
  protected $db;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nexendrie\Group */
  protected $groupModel;
  /** @var array */
  protected $names = array();
  
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
   * @return array
   */
  function getAllNames() {
    $names = $this->cache->load("users_names");
    if($names === NULL) {
      $users = $this->db->table("users");
      foreach($users as $user) {
        $names[$user->id] = (object) array(
          "id" => $user->id, "username" => $user->username, "publicname" => $user->publicname
        );
      }
      $this->cache->save("users_names", $names);
    }
    return $names;
  }
  
  /**
   * Get specified user's username and public name
   * 
   * @param int $id User's id
   * @return array
   */
  function getNames($id) {
    $names = $this->getAllNames();
    $user = Arrays::get($names, $id, false);
    return $user;
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
    $group = $this->groupModel->get($user->group);
    if(!$group) $return->title = "";
    else $return->title = $group->single_name;
    return $return;
  }
}
?>