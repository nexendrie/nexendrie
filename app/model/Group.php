<?php
namespace Nexendrie\Model;

use Nette\Utils\Arrays;

/**
 * Group Model
 *
 * @author Jakub Konečný
 */
class Group extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  
  /**
   * @param \Nette\Caching\Cache $cache
   * @param \Nette\Database\Context $db
   */
  function __construct(\Nette\Caching\Cache $cache, \Nette\Database\Context $db) {
    $this->db = $db;
    $this->cache = $cache;
  }
  
  /**
   * @param \Nette\Security\User $user
   * @return void
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * Get list of all groups
   * 
   * @return array
   */
  function listOfGroups() {
    $groups = $this->cache->load("groups");
    if($groups === NULL) {
      $groupsRows = $this->db->table("groups");
      foreach($groupsRows as $row) {
        $group = new \stdClass;
        foreach($row as $key => $value) {
          $group->$key = $value;
        }
        $groups[$group->id] = $group;
        $this->cache->save("groups", $groups);
      }
    }
    return $groups;
  }
  
  /**
   * Get number of members of specified group
   * 
   * @param int $group Group's id
   * @return int
   */
  function numberOfMembers($group) {
    return $this->db->table("users")->where("group", $group)->count("*");
  }
  
  /**
   * Get specified group
   * 
   * @param int $id Group's id
   * @return \stdClass|bool
   */
  function get($id) {
    $groups = $this->listOfGroups();
    $group = Arrays::get($groups, $id, false);
    return $group;
  }
  
  function edit($id, \Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("group", "edit")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for adding news.", 403);
    $this->db->query("UPDATE groups SET ? WHERE id=?", $data, $id);
  }
}
?>