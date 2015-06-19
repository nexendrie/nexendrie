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
  /** @var \Nette\Caching\Cache */
  protected $cache;
  
  function __construct(\Nette\Caching\Cache $cache, \Nette\Database\Context $db) {
    $this->db = $db;
    $this->cache = $cache;
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