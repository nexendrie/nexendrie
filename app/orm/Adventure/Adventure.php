<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Adventure
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property string $intro
 * @property string $epilogue
 * @property int $reward
 * @property int $level {default 55}
 * @property-read string $rewardT {virtual}
 * @property OneHasMany|AdventureNpc[] $npcs {1:m AdventureNpc::$adventure}
 * @property OneHasMany|UserAdventure[] $userAdventures {1:m UserAdventure::$adventure}
 */
class Adventure extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterRewardT() {
    return $this->localeModel->money($this->reward);
  }
}
?>