<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Orm\ItemSet as ItemSetEntity;

/**
 * Combat Model
 *
 * @author Jakub Konečný
 */
class Combat {
  /** @var Inventory */
  protected $inventoryModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  use \Nette\SmartObject;
  
  public function __construct(Inventory $inventoryModel, \Nexendrie\Orm\Model $orm) {
    $this->inventoryModel = $inventoryModel;
    $this->orm = $orm;
  }
  
  /**
   * Calculate specified user's life and max life
   *
   * @return int[]
   */
  public function calculateUserLife(UserEntity $user): array {
    $maxLife = $life = $user->maxLife;
    $hpIncrease = 0;
    $helmet = $this->inventoryModel->getHelmet($user->id);
    $set = $this->inventoryModel->getUserItemSet($user->id);
    if($helmet) {
      $hpIncrease = ($helmet->item->strength + $helmet->level) * 5;
    }
    if($set AND $set->stat === ItemSetEntity::STAT_HITPOINTS) {
      $hpIncrease += $set->bonus;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($user->id);
    if(!is_null($marriage)) {
      $hpIncrease += $marriage->hpIncrease;
    }
    $maxLife += $hpIncrease;
    $life  += $hpIncrease;
    return ["maxLife" => $maxLife, "life" => $life];
  }
  
  /**
   * Calculate specified user's damage
   */
  public function calculateUserDamage(UserEntity $user, MountEntity $mount = NULL): int {
    $damage = 0;
    $weapon = $this->inventoryModel->getWeapon($user->id);
    if($weapon) {
      $damage += $weapon->item->strength + $weapon->level;
    }
    $damageSkills = $this->orm->userSkills->findByUserAndStat($user->id, "damage");
    $set = $this->inventoryModel->getUserItemSet($user->id);
    if($damageSkills) {
      foreach($damageSkills as $damageSkill) {
        $damage += $damageSkill->skill->statIncrease * $damageSkill->level;
      }
    }
    if($mount) {
      $damage += $mount->damage;
    }
    if($set AND $set->stat === ItemSetEntity::STAT_DAMAGE) {
      $damage += $set->bonus;
    }
    return $damage;
  }
  
  /**
   * Calculate specified user's armor
   */
  public function calculateUserArmor(UserEntity $user, MountEntity $mount = NULL) {
    $armorValue = 0;
    $armor = $this->inventoryModel->getArmor($user->id);
    if($armor) {
      $armorValue += $armor->item->strength + $armor->level;
    }
    $armorSkills = $this->orm->userSkills->findByUserAndStat($user->id, "armor");
    $set = $this->inventoryModel->getUserItemSet($user->id);
    if($armorSkills) {
      foreach($armorSkills as $armorSkill) {
        $armorValue += $armorSkill->skill->statIncrease * $armorSkill->level;
      }
    }
    if($mount) {
      $armorValue += $mount->armor;
    }
    if($set AND $set->stat === ItemSetEntity::STAT_ARMOR) {
      $armorValue += $set->bonus;
    }
    return $armorValue;
  }
  
  /**
   * Get specified user's combat stats
   *
   * @return int[]
   */
  public function userCombatStats(UserEntity $user, MountEntity $mount = NULL): array {
    $stats = $this->calculateUserLife($user);
    $stats["damage"] = $this->calculateUserDamage($user, $mount);
    $stats["armor"] = $this->calculateUserArmor($user, $mount);
    return $stats;
  }
}
?>