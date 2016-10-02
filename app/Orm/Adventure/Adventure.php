<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Adventure
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property string $intro
 * @property string $epilogue
 * @property int $reward
 * @property int $level {default 55}
 * @property Event|NULL $event {m:1 Event::$adventures}
 * @property-read string $rewardT {virtual}
 * @property OneHasMany|AdventureNpc[] $npcs {1:m AdventureNpc::$adventure, orderBy=order}
 * @property OneHasMany|UserAdventure[] $userAdventures {1:m UserAdventure::$adventure}
 */
class Adventure extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterRewardT() {
    return $this->localeModel->money($this->reward);
  }
}
?>