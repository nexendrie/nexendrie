<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\PermissionDummy,
    Nette\Security\Permission,
    Nette\Caching\Cache,
    Nette\Utils\Arrays,
    Nexendrie\Orm\Model as ORM;

/**
 * Authorizator
 *
 * @author Jakub Konečný
 */
class AuthorizatorFactory {
  use \Nette\SmartObject;
  
  /** @var Cache */
  protected $cache;
  /** @var ORM */
  protected $orm;
  
  /**
   * AuthorizatorFactory constructor.
   * @param Cache $cache
   * @param ORM $orm
   */
  function __construct(Cache $cache, ORM $orm) {
    $this->cache = $cache;
    $this->orm = $orm;
  }
  
  /**
   * Get list of all groups ordered by level
   * 
   * @return \stdClass[]
   */
  function getGroups(): array {
    $groups = $this->cache->load("groups_by_level");
    if($groups === NULL) {
      $groups = [];
      $groupsRows = $this->orm->groups->findAll()->orderBy("level");
      /** @var \Nexendrie\Orm\Group $row */
      foreach($groupsRows as $row) {
        $groups[$row->id] = $row->dummy();
      }
      $this->cache->save("groups_by_level", $groups);
    }
    return $groups;
  }
  
  /**
   * Get permissions
   * 
   * @return PermissionDummy[]
   */
  function getPermissions(): array {
    $return = $this->cache->load("permissions");
    if($return === NULL) {
      $rows = $this->orm->permissions->findAll();
      /** @var \Nexendrie\Orm\Permission $row */
      foreach($rows as $row) {
        $return[] = $row->dummy();
      }
      $this->cache->save("permissions", $return);
    }
    return $return;
  }
  
  /**
  * Factory for Authorizator
  *
  * @return Permission
  */
  function create(): Permission {
    $permission = new Permission;
    
    $groups = $this->getGroups();
    $permissions = $this->getPermissions();
    
    foreach($groups as $i => $row) {
      if($row->level === 0) {
        $permission->addRole($row->singleName);
      } else {
        $parent = $groups[$i+1];
        $permission->addRole($row->singleName, $parent->singleName);
      }
    }
    
    $permission->deny("vězeň");
    foreach($permissions as $row) {
      if(!$permission->hasResource($row->resource)) {
        $permission->addResource($row->resource);
      }
      $group = Arrays::get($groups, $row->group);
      $permission->allow($group->singleName, $row->resource, $row->action);
    }
    
    return $permission;
  }
}
?>