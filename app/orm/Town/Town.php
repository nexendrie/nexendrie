<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Town
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $founded
 * @property User $owner {m:1 User::$ownedTowns}
 * @property int $price {default 5000}
 * @property bool $onMarket {default 0}
 * @property OneHasMany|User[] $denizens {1:m User::$town order:group}
 * @property OneHasMany|Monastery[] $monasteries {1:m Monastery::$town}
 * @property OneHasMany|Guild[] $guilds {1:m Guild::$town}
 * @property OneHasMany|Election[] $elections {1:m Election::$town}
 * @property OneHasMany|ElectionResult[] $electionResults {1:m ElectionResult::$town}
 * @property-read string $foundedAt {virtual}
 * @property-read string $priceT {virtual}
 */
class Town extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDate($this->founded);
  }
  
  protected function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->founded = time();
    if($this->owner->id === 0) $this->onMarket = 1;
  }
}
?>