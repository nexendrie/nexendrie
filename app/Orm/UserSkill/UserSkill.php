<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use HeroesofAbenez\Combat\CharacterEffect;
use HeroesofAbenez\Combat\SkillSpecial;
use HeroesofAbenez\Combat\Character;
use HeroesofAbenez\Combat\ICharacterEffectsProvider;

/**
 * UserSkill
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$skills}
 * @property Skill $skill {m:1 Skill::$userSkills}
 * @property int $level
 * @property int $created
 * @property int $updated
 * @property-read int $learningPrice {virtual}
 * @property-read string $learningPriceT {virtual}
 * @property-read int $jobRewardBonus {virtual}
 * @property-read int $jobSuccessRateBonus {virtual}
 */
final class UserSkill extends BaseEntity implements ICharacterEffectsProvider {
  /** Increase of success rate per skill level (in %) */
  public const LEVEL_SUCCESS_RATE = 5;
  /** Increase of income per skill level (in %) */
  public const LEVEL_BONUS_INCOME = 15;
  
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectEventsModel(\Nexendrie\Model\Events $eventsModel): void {
    $this->eventsModel = $eventsModel;
  }

  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterLearningPrice(): int {
    $price = $basePrice = $this->skill->price;
    for($i = 2; $i <= $this->level + 1; $i++) {
      $price += (int) ($basePrice / $this->skill->maxLevel);
    }
    $price -= $this->eventsModel->calculateTrainingDiscount($price);
    if($this->user->monastery && $this->user->group->path === Group::PATH_CHURCH) {
      $monasteryDiscount = $this->user->monastery->skillLearningDiscount;
      $price -= (int) ($price / 100 * $monasteryDiscount);
    }
    return $price;
  }

  protected function getterLearningPriceT(): string {
    return $this->localeModel->money($this->learningPrice);
  }
  
  protected function getterJobRewardBonus(): int {
    return $this->level * static::LEVEL_BONUS_INCOME;
  }
  
  protected function getterJobSuccessRateBonus(): int {
    return $this->level * static::LEVEL_SUCCESS_RATE;
  }
  
  public function getCombatEffects(): array {
    if($this->skill->type !== Skill::TYPE_COMBAT) {
      return [];
    }
    $bonusStats = [
      Skill::STAT_HITPOINTS => Character::STAT_MAX_HITPOINTS, Skill::STAT_DAMAGE => Character::STAT_DAMAGE,
      Skill::STAT_ARMOR => Character::STAT_DEFENSE, Skill::STAT_INITIATIVE => Character::STAT_INITIATIVE,
    ];
    $stats = [
      "id" => "skill{$this->skill->id}Effect", "type" => SkillSpecial::TYPE_BUFF,
      "duration" => CharacterEffect::DURATION_COMBAT, "valueAbsolute" => true,
      "stat" => $bonusStats[$this->skill->stat], "value" => $this->skill->statIncrease * $this->level,
    ];
    return [new CharacterEffect($stats)];
  }
}
?>