<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\User as UserEntity;
use Nexendrie\Orm\AdventureNpc;
use Nexendrie\Orm\Mount as MountEntity;
use Nexendrie\Orm\ItemSet as ItemSetEntity;
use Nexendrie\Orm\Item as ItemEntity;
use Nexendrie\Orm\Skill as SkillEntity;
use HeroesofAbenez\Combat\Character;
use HeroesofAbenez\Combat\Equipment;
use HeroesofAbenez\Combat\ConstantInitiativeFormulaParser;

/**
 * Combat Model
 *
 * @author Jakub Konečný
 */
final class CombatHelper {
  protected Inventory $inventoryModel;
  protected \Nexendrie\Orm\Model $orm;
  
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
    if($helmet !== null) {
      $hpIncrease = ($helmet->item->strength + $helmet->level) * 5;
    }
    if($set && $set->stat === ItemSetEntity::STAT_HITPOINTS) {
      $hpIncrease += $set->bonus;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($user->id);
    if($marriage !== null) {
      $hpIncrease += $marriage->hpIncrease;
    }
    $maxLife += $hpIncrease;
    $life += $hpIncrease;
    return ["maxLife" => $maxLife, "life" => $life];
  }
  
  /**
   * @throws UserNotFoundException
   */
  public function getCharacter(int $id, ?MountEntity $mount = null): Character {
    $user = $this->orm->users->getById($id);
    if($user === null) {
      throw new UserNotFoundException();
    }
    $stats = [
      "id" => $user->id, "name" => $user->publicname, "level" => 1, "strength" => 0, "dexterity" => 0,
      "intelligence" => 0, "charisma" => 0, "initiativeFormula" => "1", "gender" => $user->gender,
    ];
    $stats["constitution"] = (int) round($user->maxLife / 5);
    $equipment = [];
    foreach($user->items as $item) {
      if(in_array($item->item->type, ItemEntity::getEquipmentTypes(), true) && $item->worn) {
        $equipment[] = $item->toCombatEquipment();
      }
    }
    $character = new Character($stats, $equipment, [], [], new ConstantInitiativeFormulaParser(1));
    $character->harm($user->maxLife - $user->life);
    if($mount !== null) {
      $character->effectProviders[] = $mount;
    }
    $set = $this->inventoryModel->getUserItemSet($user->id);
    if($set !== null) {
      $character->effectProviders[] = $set;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($user->id);
    if($marriage !== null) {
      $character->effectProviders[] = $marriage;
    }
    $skills = $user->skills->get()->findBy([
      "this->skill->type" => SkillEntity::TYPE_COMBAT, "this->skill->stat!=" => SkillEntity::STAT_HITPOINTS,
    ]);
    foreach($skills as $skill) {
      $character->effectProviders[] = $skill;
    }
    return $character;
  }
  
  public function getAdventureNpc(AdventureNpc $npc): Character {
    $stats = [
      "id" => "adventureNpc{$npc->id}", "name" => $npc->name, "level" => 1, "strength" => $npc->strength * 2,
      "dexterity" => 0, "intelligence" => 0, "charisma" => 0, "initiativeFormula" => "0",
    ];
    $stats["constitution"] = (int) round($npc->hitpoints / 5);
    $armorStats = [
      "id" => $npc->id, "name" => "Armor", "slot" => Equipment::SLOT_ARMOR, "type" => null,
      "strength" => $npc->armor, "worn" => true,
    ];
    $character = new Character($stats, [new Equipment($armorStats)], [], [], new ConstantInitiativeFormulaParser($npc->initiative));
    return $character;
  }
}
?>