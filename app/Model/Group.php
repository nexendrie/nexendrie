<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Utils\Arrays,
    Nexendrie\Orm\Group as GroupEntity,
    Nexendrie\Orm\GroupDummy;

/**
 * Group Model
 *
 * @author Jakub Konečný
 * @property \Nette\Security\User $user
 */
final class Group {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nette\Caching\Cache $cache, \Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
    $this->cache = $cache;
  }
  
  public function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * Get list of all groups
   * 
   * @return GroupDummy[]
   */
  public function listOfGroups(): array {
    $groups = $this->cache->load("groups", function() {
      $groups = [];
      $groupsRows = $this->orm->groups->findAll();
      /** @var \Nexendrie\Orm\Group $row */
      foreach($groupsRows as $row) {
        $groups[$row->id] = $row->dummy();
      }
      return $groups;
    });
    return $groups;
  }
  
  /**
   * Get specified group
   */
  public function get(int $id) {
    $groups = $this->listOfGroups();
    $group = Arrays::get($groups, $id, NULL);
    return $group;
  }
  
  public function ormGet(int $id): ?GroupEntity {
    return $this->orm->groups->getById($id);
  }
  
  /**
   * Check whether specified guild exists
   */
  public function exists(int $id): bool {
    $group = $this->orm->groups->getById($id);
    return (bool) $group;
  }
  
  /**
   * Edit specified group
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws GroupNotFoundException
   */
  public function edit(int $id, array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->user->isAllowed("group", "edit")) {
      throw new MissingPermissionsException();
    }
    $group = $this->orm->groups->getById($id);
    if(is_null($group)) {
      throw new GroupNotFoundException();
    }
    foreach($data as $key => $value) {
      $group->$key = $value;
    }
    $this->orm->groups->persistAndFlush($group);
    $this->cache->remove("groups");
    $this->cache->remove("groups_by_level");
  }
}
?>