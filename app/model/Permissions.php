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
    $groups = $this->cache->load("groups_by_level");
    if($groups === NULL) {
      $groupsRows = $this->db->table("groups")
        ->order("level");
      foreach($groupsRows as $row) {
        $group = (object) array(
          "id" => $row->id, "single_name" => $row->single_name, "level" => $row->level
        );
        $groups[$group->id] = $group;
      }
      $this->cache->save("groups_by_level", $groups);
    }
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
    $return = $this->cache->load("permissions");
    if($return === NULL) {
      $rows = $this->db->table("permissions");
      foreach($rows as $row) {
        $per = new \stdClass;
        foreach($row as $key => $value) {
          $per->$key = $value;
        }
        $return[] = $per;
      }
      $this->cache->save("permissions", $return);
    }
    return $return;
  }
}
?>