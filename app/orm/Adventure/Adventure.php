<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Adventure
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $reward
 * @propery-read string $rewardT {virtual}
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