<?php
namespace Nexendrie;

use Nette\Utils\Arrays;

/**
 * Description of Permissions
 *
 * @author Jakub Konečný
 */
class Permissions extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  
  function __construct(\Nette\Caching\Cache $cache, \Nette\Database\Context $db) {
    $this->db = $db;
    $this->cache = $cache;
  }
  
  /**
   * Get list of all groups
   * 
   * @return array
   */
  function getGroups() {
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
    $groups = $this->getGroups();
    $group = Arrays::get($groups, $id, false);
    return $group;
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