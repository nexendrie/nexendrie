<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\ITavernControlFactory,
    Nexendrie\Components\TavernControl;

/**
 * Presenter Tavern
 *
 * @author Jakub Konečný
 */
class TavernPresenter extends BasePresenter {
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->mustNotBeTavelling();
  }
  
  /**
   * @param ITavernControlFactory $factory
   * @return TavernControl
   */
  protected function createComponentTavern(ITavernControlFactory $factory): TavernControl {
    return $factory->create();
  }
}
?>