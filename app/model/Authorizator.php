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
  * @param \Nexendrie\Group $groupModel
  * @return \Nette\Security\Permission
  */
  static function create(\Nexendrie\Permissions $model, \Nexendrie\Group $groupModel) {
    $permission = new \Nette\Security\Permission;
    
    $groups = $model->getGroupsByLevel();
    $permissions = $model->getPermissions();
    
    foreach($groups as $i => $row) {
      if($row->level === 0) {
        $permission->addRole($row->single_name);
      } else {
        $parent = $groups[$i+1];
        $permission->addRole($row->single_name, $parent->single_name);
      }
    }
    
    foreach($permissions as $row) {
      if(!$permission->hasResource($row->resource)) $permission->addResource($row->resource);
      $group = $groupModel->get($row->group);
      $permission->allow($group->single_name, $row->resource, $row->action);
    }
    
    return $permission;
  }
}
?>