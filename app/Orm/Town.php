<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Town
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $created
 * @property int $updated
 * @property User $owner {m:1 User::$ownedTowns}
 * @property int $price {default 5000}
 * @property bool $onMarket {default false}
 * @property OneHasMany|User[] $denizens {1:m User::$town, orderBy=group}
 * @property OneHasMany|Monastery[] $monasteries {1:m Monastery::$town}
 * @property OneHasMany|Guild[] $guilds {1:m Guild::$town}
 * @property OneHasMany|Election[] $elections {1:m Election::$town}
 * @property OneHasMany|ElectionResult[] $electionResults {1:m ElectionResult::$town}
 * @property OneHasMany|ChatMessage[] $chatMessages {1:m ChatMessage::$town}
 * @property-read string $createdAt {virtual}
 * @property-read string $priceT {virtual}
 */
final class Town extends BaseEntity {
  protected \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDate($this->created);
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
}
?>