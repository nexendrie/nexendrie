<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\PermissionDummy,
    Nette\Security\Permission,
    Nette\Caching\Cache,
    Nette\Utils\Arrays,
    Nexendrie\Orm\Model as ORM;

/**
 * Authorizator
 *
 * @author Jakub Konečný
 */
class AuthorizatorFactory {
  use \Nette\SmartObject;
  
  /** @internal */
  public const GUILD_RANK_ROLE_PREFIX = "cech";
  /** @internal */
  public const GUILD_RESOURCE_NAME = "guild";
  /** @internal */
  public const ORDER_RANK_ROLE_PREFIX = "řád";
  /** @internal */
  public const ORDER_RESOURCE_NAME = "order";
  /** @internal */
  public const ORGANIZATION_PRIVILEGES = ["manage", "upgrade", "promote", "demote", "kick",];
  
  /** @var Cache */
  protected $cache;
  /** @var ORM */
  protected $orm;
  /** @var SettingsRepository */
  protected $sr;
  
  public function __construct(Cache $cache, ORM $orm, SettingsRepository $sr) {
    $this->cache = $cache;
    $this->orm = $orm;
    $this->sr = $sr;
  }
  
  /**
   * Get list of all groups ordered by level
   * 
   * @return \stdClass[]
   */
  public function getGroups(): array {
    $groups = $this->cache->load("groups_by_level", function() {
      $groups = [];
      $groupsRows = $this->orm->groups->findAll()->orderBy("level");
      /** @var \Nexendrie\Orm\Group $row */
      foreach($groupsRows as $row) {
        $groups[$row->id] = $row->dummy();
      }
      return $groups;
    });
    return $groups;
  }
  
  /**
   * @return string[]
   */
  public function getGuildRanks(): array {
    $ranks = $this->cache->load("guild_ranks", function() {
      $ranks = [];
      $rows = $this->orm->guildRanks->findAll();
      /** @var \Nexendrie\Orm\GuildRank $row */
      foreach($rows as $row) {
        $ranks[$row->id] = $row->name;
      }
      return $ranks;
    });
    return $ranks;
  }
  
  /**
   * @return string[]
   */
  public function getOrderRanks(): array {
    $ranks = $this->cache->load("order_ranks", function() {
      $ranks = [];
      $rows = $this->orm->orderRanks->findAll();
      /** @var \Nexendrie\Orm\OrderRank $row */
      foreach($rows as $row) {
        $ranks[$row->id] = $row->name;
      }
      return $ranks;
    });
    return $ranks;
  }
  
  /**
   * Get permissions
   *
   * @return PermissionDummy[]
   */
  public function getPermissions(): array {
    $return = $this->cache->load("permissions", function() {
      $return = [];
      $rows = $this->orm->permissions->findAll();
      /** @var \Nexendrie\Orm\Permission $row */
      foreach($rows as $row) {
        $return[] = $row->dummy();
      }
      return $return;
    });
    return $return;
  }
  
  protected function addRanks(array $ranks, Permission &$permission, string $type): void {
    foreach($ranks as $id => $rank) {
      $parent = NULL;
      if($id > 1) {
        $parent = $type .  "^" . $ranks[$id-1];
      }
      $permission->addRole($type .  "^" . $rank, $parent);
    }
  }
  
  protected function addOrganizationPrivileges(array $roles, Permission &$permission, string $name): void {
    $permission->addResource($name);
    $lowestRank = min(array_keys($roles));
    $highestRank = max(array_keys($roles));
    $permission->deny($roles[$lowestRank]);
    foreach(static::ORGANIZATION_PRIVILEGES as $privilege) {
      $permission->allow($roles[$highestRank], $name, $privilege);
    }
  }
  
  /**
  * Factory for Authorizator
  */
  public function create(): Permission {
    $permission = new Permission();
    
    $groups = $this->getGroups();
    $guildRanks = $this->getGuildRanks();
    $orderRanks = $this->getOrderRanks();
    $permissions = $this->getPermissions();
    
    foreach($groups as $i => $row) {
      $parent = NULL;
      if($row->level !== 0) {
        $parent = $groups[$i+1]->singleName;
      }
      $permission->addRole($row->singleName, $parent);
    }
    $this->addRanks($guildRanks, $permission, static::GUILD_RANK_ROLE_PREFIX);
    $addPrefix = function(&$value, $key, $prefix) {
      $value = $prefix . "^" . $value;
    };
    array_walk($guildRanks, $addPrefix, static::GUILD_RANK_ROLE_PREFIX);
    $this->addOrganizationPrivileges($guildRanks, $permission, static::GUILD_RESOURCE_NAME);
    $this->addRanks($orderRanks, $permission, static::ORDER_RANK_ROLE_PREFIX);
    array_walk($orderRanks, $addPrefix, static::ORDER_RANK_ROLE_PREFIX);
    $this->addOrganizationPrivileges($orderRanks, $permission, static::ORDER_RESOURCE_NAME);
    
    $bannedRole = $groups[$this->sr->settings["roles"]["bannedRole"]]->singleName;
    $permission->deny($bannedRole);
    foreach($permissions as $row) {
      if(!$permission->hasResource($row->resource)) {
        $permission->addResource($row->resource);
      }
      $group = Arrays::get($groups, $row->group);
      $permission->allow($group->singleName, $row->resource, $row->action);
    }
    
    return $permission;
  }
}
?>