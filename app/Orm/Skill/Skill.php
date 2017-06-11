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
 * @property string $type {enum self::TYPE_*}
 * @property string|NULL $stat {enum self::STAT_*} {default NULL}
 * @property-read string|NULL $statCZ {virtual}
 * @property int $statIncrease {default 0}
 * @property OneHasMany|Job[] $jobs {1:m Job::$neededSkill}
 * @property OneHasMany|UserSkill[] $userSkills {1:m UserSkill::$skill}
 * @property OneHasMany|Guild[] $guilds {1:m Guild::$skill}
 * @property-read string $effect {virtual}
 */
class Skill extends \Nextras\Orm\Entity\Entity {
  const TYPE_WORK = "work";
  const TYPE_COMBAT = "combat";
  const STAT_HITPOINTS = "hitpoints";
  const STAT_DAMAGE = "damage";
  const STAT_ARMOR = "armor";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  /**
   * @return string[]
   */
  static function getTypes(): array {
    return [
      self::TYPE_WORK => "práce",
      self::TYPE_COMBAT => "boj",
    ];
  }
  
  /**
   * @return string[]
   */
  static function getStats(): array {
    return [
      self::STAT_HITPOINTS => "maximum životů",
      self::STAT_DAMAGE => "poškození",
      self::STAT_ARMOR => "brnění",
    ];
  }
  
  protected function getterStatCZ() {
    return ($this->stat != NULL) ? self::getStats()[$this->stat] : NULL;
  }
  
  protected function getterEffect(): string {
    if($this->type === self::TYPE_WORK) {
      return "";
    }
    return $this->statCZ . " +" . $this->statIncrease;
  }
}
?>