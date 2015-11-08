<?php
namespace Nexendrie\Model;

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
  
  function __construct(\Nexendrie\Model\Equipment $equipmentModel, \Nexendrie\Orm\Model $orm) {
    $this->equipmentModel = $equipmentModel;
    $this->orm = $orm;
  }
  
  /**
   * Get specified user's combat stats
   * 
   * @param int $userId
   * @return int[]
   * @throws UserNotFoundException
   */
  function userCombatStats($userId) {
    $user = $this->orm->users->getById($userId);
    if(!$user) throw new UserNotFoundException;
    $stats = array();
    $stats["maxLife"] = $stats["life"] = $user->maxLife;
    $stats["damage"] = $stats["armor"] = 0;
    $weapon = $this->equipmentModel->getWeapon($userId);
    $armor = $this->equipmentModel->getArmor($userId);
    if($weapon) $stats["damage"] += $weapon->strength;
    if($armor) $stats["armor"] += $armor->strength;
    $damageSkill = $this->orm->userSkills->getByUserAndStat($userId, "damage");
    $armorSkill = $this->orm->userSkills->getByUserAndStat($userId, "armor");
    if($damageSkill) $stats["damage"] += $damageSkill->skill->statIncrease * $damageSkill->level;
    if($armorSkill) $stats["armor"] += $armorSkill->skill->statIncrease * $damageSkill->level;
    return $stats;
  }
}
?>