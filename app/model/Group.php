<?php
namespace Nexendrie;

use Nette\Utils\Arrays;

/**
 * Group Model
 *
 * @author Jakub Konečný
 */
class Group extends \Nette\Object {
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
  function listOfGroups() {
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
   * Get specified group
   * 
   * @param int $id Group's id
   * @return \stdClass|bool
   */
  function get($id) {
    $groups = $this->listOfGroups();
    $group = Arrays::get($groups, $id, false);
    return $group;
  }
  
}
?>