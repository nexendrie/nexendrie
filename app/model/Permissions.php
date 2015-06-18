<?php
namespace Nexendrie;

/**
 * Description of Permissions
 *
 * @author Jakub Konečný
 */
class Permissions extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  
  function __construct(\Nette\Database\Context $db) {
    $this->db = $db;
  }
  
  /**
   * Get list of all groups
   * 
   * @return array
   */
  function getGroups() {
    $groups = $this->db->table("groups");
    return $groups;
  }
  
  /**
   * Get list of all groups ordered by level
   * 
   * @return array
   */
  function getGroupsByLevel() {
    $groups = $this->db->table("groups")
      ->order("level");
    return $groups;
  }
  
  /**
   * Get specified group
   * 
   * @param int $id Group's id
   * @return \Nette\Database\Table\ActiveRow|bool
   */
  function getGroup($id) {
    $row = $this->db->table("groups")->get($id);
    if(!$row) {
      return false;
    } else {
      $group = new \stdClass;
      foreach($row as $key => $value) {
        $group->$key = $value;
      }
      return $group;
    }
  }
  
  /**
   * Get permissions
   * 
   * @return array
   */
  function getPermissions() {
    $return = $this->db->table("permissions");
    return $return;
  }
}
?>