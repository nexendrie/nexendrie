<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Skill
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property int $price
 * @property-read string $priceT {virtual}
 * @property int $maxLevel
 * @property string $type {enum static::TYPE_*}
 * @property string|NULL $stat {enum static::STAT_*} {default NULL}
 * @property-read string|NULL $statCZ {virtual}
 * @property int $statIncrease {default 0}
 * @property OneHasMany|Job[] $jobs {1:m Job::$neededSkill}
 * @property OneHasMany|UserSkill[] $userSkills {1:m UserSkill::$skill}
 * @property OneHasMany|Guild[] $guilds {1:m Guild::$skill}
 * @property-read string $effect {virtual}
 */
final class Skill extends \Nextras\Orm\Entity\Entity {
  public const TYPE_WORK = "work";
  public const TYPE_COMBAT = "combat";
  public const STAT_HITPOINTS = "hitpoints";
  public const STAT_DAMAGE = "damage";
  public const STAT_ARMOR = "armor";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  /**
   * @return string[]
   */
  public static function getTypes(): array {
    return [
      static::TYPE_WORK => "práce",
      static::TYPE_COMBAT => "boj",
    ];
  }
  
  /**
   * @return string[]
   */
  public static function getStats(): array {
    return [
      static::STAT_HITPOINTS => "maximum životů",
      static::STAT_DAMAGE => "poškození",
      static::STAT_ARMOR => "brnění",
    ];
  }
  
  protected function getterStatCZ(): ?string {
    return ($this->stat !== NULL) ? static::getStats()[$this->stat] : NULL;
  }
  
  protected function getterEffect(): string {
    if($this->type === static::TYPE_WORK) {
      return "";
    }
    return $this->statCZ . " +" . $this->statIncrease;
  }
}
?>