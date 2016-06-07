<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Mount as MountEntity;

/**
 * Combat Model
 *
 * @author Jakub Konečný
 */
class Combat extends \Nette\Object {
  /** @var \Nexendrie\Model\Inventory */
  protected $inventoryModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(Inventory $inventoryModel, \Nexendrie\Orm\Model $orm) {
    $this->inventoryModel = $inventoryModel;
    $this->orm = $orm;
  }
  
  /**
   * Calculate specified user's life and max life
   * 
   * @param UserEntity $user
   * @return int[]
   */
  function calculateUserLife(UserEntity $user) {
    $maxLife = $life = $user->maxLife;
    $hpIncrease = 0;
    $helmet = $this->inventoryModel->getHelmet($user->id);
    if($helmet) {
      $hpIncrease = ($helmet->item->strength + $helmet->level) * 5;
      $maxLife += $hpIncrease;
      $life  += $hpIncrease;
    }
    return array("maxLife" => $maxLife, "life" => $life);
  }
  
  /**
   * Calculate specified user's damage
   * 
   * @param UserEntity $user
   * @param MountEntity|NULL $mount
   * @return int
   */
  function calculateUserDamage(UserEntity $user, MountEntity $mount = NULL) {
    $damage = 0;
    $weapon = $this->inventoryModel->getWeapon($user->id);
    if($weapon) $damage += $weapon->item->strength + $weapon->level;
    $damageSkills = $this->orm->userSkills->findByUserAndStat($user->id, "damage");
    if($damageSkills) {
      foreach($damageSkills as $damageSkill) {
        $damage += $damageSkill->skill->statIncrease * $damageSkill->level;
      }
    }
    if($mount) $damage += $mount->damage;
    return $damage;
  }
  
  /**
   * Calculate specified user's armor
   * 
   * @param UserEntity $user
   * @param MountEntity|NULL $mount
   * @return int
   */
  function calculateUserArmor(UserEntity $user, MountEntity $mount = NULL) {
    $armorValue = 0;
    $armor = $this->inventoryModel->getArmor($user->id);
    if($armor) $armorValue += $armor->item->strength + $armor->level;
    $armorSkills = $this->orm->userSkills->findByUserAndStat($user->id, "armor");
    if($armorSkills) {
      foreach($armorSkills as $armorSkill) {
        $armorValue += $armorSkill->skill->statIncrease * $armorSkill->level;
      }
    }
    if($mount) $armorValue += $mount->armor;
    return $armorValue;
  }
  
  /**
   * Get specified user's combat stats
   * 
   * @param UserEntity $user
   * @param MountEntity|NULL $mount
   * @return int[]
   */
  function userCombatStats(UserEntity $user, MountEntity $mount = NULL) {
    $stats = $this->calculateUserLife($user);
    $stats["damage"] = $this->calculateUserDamage($user, $mount);
    $stats["armor"] = $this->calculateUserArmor($user, $mount);
    return $stats;
  }
}
?>