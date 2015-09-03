<?php
namespace Nexendrie\Model;

use Nette\Utils\Arrays,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Group Model
 *
 * @author Jakub Konečný
 */
class Group extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  
  /**
   * @param \Nette\Caching\Cache $cache
   * @param \Nette\Database\Context $db
   */
  function __construct(\Nette\Caching\Cache $cache, \Nette\Database\Context $db, \Nexendrie\Orm\Model $orm) {
    $this->db = $db;
    $this->orm = $orm;
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
   * @return \stdClass[]
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
      }
    $this->cache->save("groups", $groups);
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
    return $this->orm->users->findByGroup($group)->count();
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
   * @param int $id
   * @return GroupEntity|bool
   */
  function ormGet($id) {
    $group = $this->orm->groups->getById($id);
    if(!$group) return false;
    else return $group;
  }
  
  /**
   * Get name of specified group
   * 
   * @deprecated
   * @param int $id Group's id
   * @return string
   */
  function getName($id) {
    $group = $this->get($id);
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
    $group = $this->orm->groups->getById($id);
    return (bool) $group;
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
    $group = $this->orm->groups->getById($id);
    foreach($data as $key => $value) {
      $group->$key = $value;
    }
    $this->orm->groups->persistAndFlush($group);
  }
  
  /**
   * Get members of specified guild
   * 
   * @deprecated
   * @param int $id Guild's id
   * @return \stdClass[]
   * @throws \Nette\Application\BadRequestException
   */
  function members($id) {
    $group = $this->orm->groups->getById($id);
    if(!$group) throw new \Nette\Application\BadRequestException("Specified guild does not exist.");
    $return = array();
    foreach($group->members as $user) {
      $return[] = (object) array(
        "id" => $user->id, "username" => $user->username, "publicname" => $user->publicname
      );
    }
    return $return;
  }
}
?>