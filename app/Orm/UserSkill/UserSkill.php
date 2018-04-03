<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use HeroesofAbenez\Combat\CharacterEffect,
    HeroesofAbenez\Combat\SkillSpecial,
    HeroesofAbenez\Combat\ICharacterEffectProvider;

/**
 * UserSkill
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$skills}
 * @property Skill $skill {m:1 Skill::$userSkills}
 * @property int $level
 */
class UserSkill extends \Nextras\Orm\Entity\Entity implements ICharacterEffectProvider {
  public function toCombatEffect(): ?CharacterEffect {
    if($this->skill->type !== Skill::TYPE_COMBAT) {
      return NULL;
    }
    $bonusStats = [
      Skill::STAT_HITPOINTS => SkillSpecial::STAT_HITPOINTS, Skill::STAT_DAMAGE => SkillSpecial::STAT_DAMAGE,
      Skill::STAT_ARMOR => SkillSpecial::STAT_DEFENSE,
    ];
    $stats = [
      "id" => "skill{$this->skill->id}Effect", "type" => SkillSpecial::TYPE_BUFF,
      "duration" => CharacterEffect::DURATION_COMBAT, "source" => CharacterEffect::SOURCE_EQUIPMENT,
      "stat" => $bonusStats[$this->skill->stat], "value" => $this->skill->statIncrease * $this->level,
    ];
    return new CharacterEffect($stats);
  }
}
?>