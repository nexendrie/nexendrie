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
    foreach($groups as $i => $row) {
      if($row->level === 0) {
        $permission->addRole($row->single_name);
      } else {
        $parent = $groups[$i+1];
        $permission->addRole($row->single_name, $parent->single_name);
      }
    }
    
    return $permission;
  }
}
?>