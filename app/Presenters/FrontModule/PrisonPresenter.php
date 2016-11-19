<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\PrisonControlFactory,
    Nexendrie\Components\PrisonControl;

/**
 * Presenter Prison
 *
 * @author Jakub Konečný
 */
class PrisonPresenter extends BasePresenter {
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    if(!$this->user->isLoggedIn()) {
      $this->redirect("Homepage:");
    } elseif(!$this->user->identity->banned) {
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param PrisonControlFactory $factory
   * @return PrisonControl
   */
  protected function createComponentPrison(PrisonControlFactory $factory): PrisonControl {
    return $factory->create();
  }
}
?>