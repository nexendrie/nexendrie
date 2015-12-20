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
   * Get specified user's combat stats
   * 
   * @param UserEntity $user
   * @param MountEntity|NULL $mount
   * @return int[]
   */
  function userCombatStats(UserEntity $user, MountEntity $mount = NULL) {
    $stats = array();
    $stats["maxLife"] = $stats["life"] = $user->maxLife;
    $stats["damage"] = $stats["armor"] = 0;
    $weapon = $this->inventoryModel->getWeapon($user->id);
    $armor = $this->inventoryModel->getArmor($user->id);
    $helmet = $this->inventoryModel->getHelmet($user->id);
    if($weapon) $stats["damage"] += $weapon->item->strength + $weapon->level;
    if($armor) $stats["armor"] += $armor->item->strength + $armor->level;
    if($helmet) {
      $hpIncrease = ($helmet->item->strength + $helmet->level) * 5;
      $stats["maxLife"] += $hpIncrease;
      $stats["life"]  += $hpIncrease;
    }
    $damageSkills = $this->orm->userSkills->findByUserAndStat($user->id, "damage");
    $armorSkills = $this->orm->userSkills->findByUserAndStat($user->id, "armor");
    if($damageSkills) {
      foreach($damageSkills as $damageSkill) {
        $stats["damage"] += $damageSkill->skill->statIncrease * $damageSkill->level;
      }
    }
    if($armorSkills) {
      foreach($armorSkills as $armorSkill) {
        $stats["armor"] += $armorSkill->skill->statIncrease * $damageSkill->level;
      }
    }
    if($mount) {
      $stats["damage"] += $mount->damage;
      $stats["armor"] += $mount->armor;
    }
    return $stats;
  }
}
?>