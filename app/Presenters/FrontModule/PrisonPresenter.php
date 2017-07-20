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
  protected function startup(): void {
    parent::startup();
    if(!$this->user->isLoggedIn()) {
      $this->redirect("Homepage:");
    } elseif(!$this->user->identity->banned) {
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentPrison(IPrisonControlFactory $factory): PrisonControl {
    return $factory->create();
  }
}
?>