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
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  
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
  
  function setProfileModel(\Nexendrie\Model\Profile $profileModel) {
    $this->profileModel = $profileModel;
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
  
  /**
   * Get name of specified group
   * 
   * @param int $id Group's id
   * @return string
   */
  function getName($id) {
    $groups = $this->listOfGroups();
    $group = Arrays::get($groups, $id, false);
    if(!$group) return "";
    else return $group->name;
  }
  
  /**
   * Check whetever specified guild exists
   * 
   * @param int $id Guild's id
   * @return bool
   */
  function exists($id) {
    $row = $this->db->table("groups")
      ->where("id", $id);
    return (bool) $row->count("*");
  }
  
  /**
   * Edit specified group
   * 
   * @param type $id Group's id
   * @param \Nette\Utils\ArrayHash $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @return void
   */
  function edit($id, \Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("group", "edit")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for adding news.", 403);
    $this->db->query("UPDATE groups SET ? WHERE id=?", $data, $id);
  }
  
  /**
   * Get members of specified guild
   * 
   * @param int $group Guild's id
   * @return array
   * @throws \Nette\Application\BadRequestException
   */
  function members($group) {
    if(!$this->exists($group)) throw new \Nette\Application\BadRequestException("Specified guild does not exist.");
    $return = array();
    $members = $this->db->table("users")
      ->where("group", $group);
    foreach($members as $member) {
      $return[] = $this->profileModel->getNames($member->id);
    }
    return $return;
  }
}
?>