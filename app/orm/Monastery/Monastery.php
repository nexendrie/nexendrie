<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Monastery
 *
 * @author Jakub Konečný
 * @property string $name
 * @property User $leader {m:1 User::$monasteriesLed}
 * @property Town $town {m:1 Town::$monasteries}
 * @property int $founded
 * @property OneHasMany|User[] $members {1:m User::$monastery}
 * @property-read string $foundedAt {virtual}
 */
class Monastery extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDateTime($this->founded);
  }
}
?>