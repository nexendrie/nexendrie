<?php
namespace Nexendrie\Model;

/**
 * Authorizator
 *
 * @author Jakub Konečný
 */
class Authorizator extends \Nette\Object {
  /**
   * Get list of all groups ordered by level
   * 
   * @param \Nette\Caching\Cache $cache
   * @param \Nette\Database\Context $db
   * @return \stdClass[]
   */
  static function getGroups(\Nette\Caching\Cache $cache, \Nette\Database\Context $db) {
    $groups = $cache->load("groups_by_level");
    if($groups === NULL) {
      $groupsRows = $db->table("groups")
        ->order("level");
      foreach($groupsRows as $row) {
        $group = (object) array(
          "id" => $row->id, "single_name" => $row->single_name, "level" => $row->level
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
   * @param \Nette\Caching\Cache $cache
   * @param \Nette\Database\Context $db
   * @return \stdClass[]
   */
  static function getPermissions(\Nette\Caching\Cache $cache, \Nette\Database\Context $db) {
    $return = $cache->load("permissions");
    if($return === NULL) {
      $rows = $db->table("permissions");
      foreach($rows as $row) {
        $per = new \stdClass;
        foreach($row as $key => $value) {
          $per->$key = $value;
        }
        $return[] = $per;
      }
      $cache->save("permissions", $return);
    }
    return $return;
  }
  
  /**
  * Factory for Authorizator
  * 
  * @param \HeroesofAbenez\Permissions $model
  * @return \Nette\Security\Permission
  */
  static function create(\Nette\Caching\Cache $cache, \Nette\Database\Context $db) {
    $permission = new \Nette\Security\Permission;
    
    $groups = self::getGroups($cache, $db);
    $permissions = self::getPermissions($cache, $db);
    
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
      $group = \Nette\Utils\Arrays::get($groups, $row->group);
      $permission->allow($group->single_name, $row->resource, $row->action);
    }
    
    return $permission;
  }
}
?>