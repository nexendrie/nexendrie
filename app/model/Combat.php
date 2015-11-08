<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\User as UserEntity;

/**
 * Combat Model
 *
 * @author Jakub Konečný
 */
class Combat extends \Nette\Object {
  /** @var \Nexendrie\Model\Equipment */
  protected $equipmentModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(Equipment $equipmentModel, \Nexendrie\Orm\Model $orm) {
    $this->equipmentModel = $equipmentModel;
    $this->orm = $orm;
  }
  
  /**
   * Get specified user's combat stats
   * 
   * @param UserEntity $user
   * @return int[]
   */
  function userCombatStats(UserEntity $user) {
    $stats = array();
    $stats["maxLife"] = $stats["life"] = $user->maxLife;
    $stats["damage"] = $stats["armor"] = 0;
    $weapon = $this->equipmentModel->getWeapon($user->id);
    $armor = $this->equipmentModel->getArmor($user->id);
    if($weapon) $stats["damage"] += $weapon->strength;
    if($armor) $stats["armor"] += $armor->strength;
    $damageSkill = $this->orm->userSkills->getByUserAndStat($user->id, "damage");
    $armorSkill = $this->orm->userSkills->getByUserAndStat($user->id, "armor");
    if($damageSkill) $stats["damage"] += $damageSkill->skill->statIncrease * $damageSkill->level;
    if($armorSkill) $stats["armor"] += $armorSkill->skill->statIncrease * $damageSkill->level;
    return $stats;
  }
}
?>