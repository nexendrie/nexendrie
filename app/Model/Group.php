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
class Group {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  /**
   * @param \Nette\Caching\Cache $cache
   * @param \Nexendrie\Orm\Model $orm
   */
  function __construct(\Nette\Caching\Cache $cache, \Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
    $this->cache = $cache;
  }
  
  /**
   * @param \Nette\Security\User $user
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * Get list of all groups
   * 
   * @return GroupDummy[]
   */
  function listOfGroups(): array {
    $groups = $this->cache->load("groups");
    if($groups === NULL) {
      $groups = [];
      $groupsRows = $this->orm->groups->findAll();
      /** @var \Nexendrie\Orm\Group $row */
      foreach($groupsRows as $row) {
        $groups[$row->id] = $row->dummy();
      }
      $this->cache->save("groups", $groups);
    }
    return $groups;
  }
  
  /**
   * Get specified group
   * 
   * @param int $id Group's id
   * @return GroupDummy|NULL
   */
  function get(int $id) {
    $groups = $this->listOfGroups();
    $group = Arrays::get($groups, $id, NULL);
    return $group;
  }
  
  /**
   * Get group of specified level
   * 
   * @param int $level Group's level
   * @return GroupDummy|NULL
   */
  function getByLevel(int $level): ?GroupDummy {
    $groups = $this->listOfGroups();
    foreach($groups as $group) {
      if($group->level === $level) {
        return $group;
      }
    }
    return NULL;
  }
  
  /**
   * @param int $id
   * @return GroupEntity|NULL
   */
  function ormGet(int $id): ?GroupEntity {
    $group = $this->orm->groups->getById($id);
    if(!$group) {
      return NULL;
    }
    else {
      return $group;
    }
  }
  
  /**
   * Check whetever specified guild exists
   *
   * @param int $id Guild's id
   * @return bool
   */
  function exists(int $id): bool {
    $group = $this->orm->groups->getById($id);
    return (bool) $group;
  }
  
  /**
   * Edit specified group
   * 
   * @param int $id Group's id
   * @param array $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @return void
   */
  function edit(int $id, array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->user->isAllowed("group", "edit")) {
      throw new MissingPermissionsException();
    }
    $group = $this->orm->groups->getById($id);
    foreach($data as $key => $value) {
      $group->$key = $value;
    }
    $this->orm->groups->persistAndFlush($group);
    $this->cache->remove("groups");
    $this->cache->remove("groups_by_level");
  }
}
?>