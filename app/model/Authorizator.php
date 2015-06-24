<?php
namespace Nexendrie;

/**
 * Authorizator
 *
 * @author Jakub Konečný
 */
class Authorizator extends \Nette\Object {
  /**
  * Factory for Authorizator
  * 
  * @param \HeroesofAbenez\Permissions $model
  * @return \Nette\Security\Permission
  */
  static function create(\Nexendrie\Permissions $model) {
    $permission = new \Nette\Security\Permission;
    
    $groups = $model->getGroups();
    $permissions = $model->getPermissions();
    
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