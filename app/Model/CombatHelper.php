<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\AdventureNpc,
    Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Orm\ItemSet as ItemSetEntity,
    Nexendrie\Orm\Item as ItemEntity,
    Nexendrie\Orm\Skill as SkillEntity,
    HeroesofAbenez\Combat\Character,
    HeroesofAbenez\Combat\Equipment,
    HeroesofAbenez\Combat\CharacterEffect,
    HeroesofAbenez\Combat\SkillSpecial;

/**
 * Combat Model
 *
 * @author Jakub Konečný
 */
class CombatHelper {
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
    if(!is_null($helmet)) {
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
  
  protected function accuracyCombatEffect(): CharacterEffect {
    $stats = [
      "id" => "accuracy", "type" => SkillSpecial::TYPE_BUFF, "stat" => SkillSpecial::STAT_HIT,
      "value" => 1000, "source" => CharacterEffect::SOURCE_EQUIPMENT, "duration" => CharacterEffect::DURATION_FOREVER,
    ];
    return new CharacterEffect($stats);
  }
  
  /**
   * @throws UserNotFoundException
   */
  public function getCharacter(int $id, ?MountEntity $mount = NULL): Character {
    $user = $this->orm->users->getById($id);
    if(is_null($user)) {
      throw new UserNotFoundException();
    }
    $stats = [
      "id" => $user->id, "name" => $user->publicname, "level" => 1, "strength" => 0, "dexterity" => 0,
      "intelligence" => 0, "charisma" => 0, "initiativeFormula" => "1d1+CON/1", "gender" => $user->gender,
    ];
    $stats["constitution"] = (int) round($user->maxLife / 5);
    $equipment = [];
    foreach($user->items as $item) {
      if(in_array($item->item->type, ItemEntity::getEquipmentTypes(), true) AND $item->worn) {
        $equipment[] = $item->toCombatEquipment();
      }
    }
    $character = new Character($stats, $equipment);
    $character->harm($user->maxLife - $user->life);
    $character->addEffect($this->accuracyCombatEffect());
    if(!is_null($mount)) {
      $character->addEffect($mount->toCombatDamageEffect());
      $character->addEffect($mount->toCombatDefenseEffect());
    }
    $set = $this->inventoryModel->getUserItemSet($user->id);
    if(!is_null($set)) {
      $character->addEffectProvider($set);
    }
    $marriage = $this->orm->marriages->getActiveMarriage($user->id);
    if(!is_null($marriage)) {
      $character->addEffectProvider($marriage);
    }
    foreach($user->skills as $skill) {
      if($skill->skill->type === SkillEntity::TYPE_COMBAT AND $skill->skill->stat !== SkillEntity::STAT_HITPOINTS) {
        $character->addEffectProvider($skill);
      }
    }
    return $character;
  }
  
  public function getAdventureNpc(AdventureNpc $npc): Character {
    $stats = [
      "id" => "adventureNpc{$npc->id}", "name" => $npc->name, "level" => 1, "strength" => $npc->strength * 2,
      "dexterity" => 0, "intelligence" => 0, "charisma" => 0, "initiativeFormula" => "1d1+CHAR/5",
    ];
    $stats["constitution"] = (int) round($npc->hitpoints / 5);
    $armorStats = [
      "id" => $npc->id, "name" => "Armor", "slot" => Equipment::SLOT_ARMOR, "type" => NULL,
      "strength" => $npc->armor, "worn" => true,
    ];
    $character = new Character($stats, [new Equipment($armorStats)]);
    $character->addEffect($this->accuracyCombatEffect());
    return $character;
  }
}
?>