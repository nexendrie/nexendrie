<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Guild
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $level {default 1}
 * @property int $founded
 * @property-read string $foundedAt {virtual}
 * @property Town $town {m:1 Town::$guilds}
 * @property int $money
 * @property-read string $moneyT {virtual}
 * @property OneHasMany|User[] $members {1:m User::$guild order:guildRank,DESC}
 */
class Guild extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterLevel($value) {
    if($value < 1) return 1;
    elseif($value > 9) return 9;
    else return $value;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDateTime($this->founded);
  }
  
  protected function getterMoneyT() {
    return $this->localeModel->money($this->money);
  }
}
?>