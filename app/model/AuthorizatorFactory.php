<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\PermissionDummy,
    Nette\Security\Permission,
    Nette\Caching\Cache,
    Nette\Utils\Arrays;

/**
 * Authorizator
 *
 * @author Jakub Konečný
 */
class AuthorizatorFactory extends \Nette\Object {
  /**
   * Get list of all groups ordered by level
   * 
   * @param Cache $cache
   * @param \Nexendrie\Orm\Model $orm
   * @return \stdClass[]
   */
  static function getGroups(Cache $cache, \Nexendrie\Orm\Model $orm) {
    $groups = $cache->load("groups_by_level");
    if($groups === NULL) {
      $groups = array();
      $groupsRows = $orm->groups->findAll()->orderBy("level");
      foreach($groupsRows as $row) {
        $group = (object) array(
          "id" => $row->id, "single_name" => $row->singleName, "level" => $row->level
        );
        $groups[$group->id] = $group;
      }
      $cache->save("groups_by_level", $groups);
    }
    return $groups;
  }
  
  /**
   * Get permissions
   * 
   * @param Cache $cache
   * @param \Nexendrie\Orm\Model $orm
   * @return PermissionDummy[]
   */
  static function getPermissions(Cache $cache, \Nexendrie\Orm\Model $orm) {
    $return = $cache->load("permissions");
    if($return === NULL) {
      $rows = $orm->permissions->findAll();
      foreach($rows as $row) {
        $return[] = $row->dummy();
      }
      $cache->save("permissions", $return);
    }
    return $return;
  }
  
  /**
  * Factory for Authorizator
  * 
  * @param  $cache
  * @param \Nexendrie\Orm\Model $orm
  * @return Permission
  */
  static function create(Cache $cache, \Nexendrie\Orm\Model $orm) {
    $permission = new Permission;
    
    $groups = self::getGroups($cache, $orm);
    $permissions = self::getPermissions($cache, $orm);
    
    foreach($groups as $i => $row) {
      if($row->level === 0) {
        $permission->addRole($row->single_name);
      } else {
        $parent = $groups[$i+1];
        $permission->addRole($row->single_name, $parent->single_name);
      }
    }
    
    $permission->deny("vězeň");
    foreach($permissions as $row) {
      if(!$permission->hasResource($row->resource)) $permission->addResource($row->resource);
      $group = Arrays::get($groups, $row->group);
      $permission->allow($group->single_name, $row->resource, $row->action);
    }
    
    return $permission;
  }
}
?>