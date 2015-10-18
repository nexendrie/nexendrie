<?php
namespace Nexendrie\Model;

use Nette\Utils\Arrays,
    Nexendrie\Orm\Group as GroupEntity,
    Nexendrie\Orm\GroupDummy;

/**
 * Group Model
 *
 * @author Jakub Konečný
 */
class Group extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  
  /**
   * @param \Nette\Caching\Cache $cache
   * @param \Nexendrie\Orm\Model $orm
   */
  function __construct(\Nette\Caching\Cache $cache, \Nexendrie\Orm\Model $orm) {
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
   * @return GroupDummy[]
   */
  function listOfGroups() {
    $groups = $this->cache->load("groups");
    if($groups === NULL) {
      $groups = array();
      $groupsRows = $this->orm->groups->findAll();
      foreach($groupsRows as $row) {
        $groups[$row->id] = $row->dummy();
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
   * @param array $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @return void
   */
  function edit($id, array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    if(!$this->user->isAllowed("group", "edit")) throw new MissingPermissionsException("You don't have permissions for adding news.");
    $group = $this->orm->groups->getById($id);
    foreach($data as $key => $value) {
      $group->$key = $value;
    }
    $this->orm->groups->persistAndFlush($group);
    $this->cache->remove("groups");
  }
}

class GroupNotFoundException extends RecordNotFoundException {
  
}
?>