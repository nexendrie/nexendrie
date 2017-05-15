<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IPrisonControlFactory,
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
   * @param IPrisonControlFactory $factory
   * @return PrisonControl
   */
  protected function createComponentPrison(IPrisonControlFactory $factory): PrisonControl {
    return $factory->create();
  }
}
?>