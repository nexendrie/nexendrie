<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Caching\Cache;
use Nette\Utils\Arrays;
use Nexendrie\Orm\Group as GroupEntity;
use Nexendrie\Orm\GroupDummy;
use Nexendrie\Orm\Model as ORM;

/**
 * Group Model
 *
 * @author Jakub Konečný
 * @property-write \Nette\Security\User $user
 */
final class Group {
  private \Nette\Security\User $user;
  
  use \Nette\SmartObject;
  
  public function __construct(private readonly Cache $cache, private readonly ORM $orm) {
  }
  
  protected function setUser(\Nette\Security\User $user): void {
    $this->user = $user;
  }
  
  /**
   * Get list of all groups
   * 
   * @return GroupDummy[]
   */
  public function listOfGroups(): array {
    return $this->cache->load("groups", function(): array {
      $groups = [];
      $groupsRows = $this->orm->groups->findAll();
      foreach($groupsRows as $row) {
        $groups[$row->id] = $row->dummy();
      }
      return $groups;
    });
  }
  
  /**
   * Get specified group
   */
  public function get(int $id): ?GroupDummy {
    $groups = $this->listOfGroups();
    $group = Arrays::get($groups, $id, null);
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
    return $group !== null;
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
    if($group === null) {
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