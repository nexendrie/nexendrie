<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\TavernControlFactory,
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
   * @param TavernControlFactory $factory
   * @return TavernControl
   */
  protected function createComponentTavern(TavernControlFactory $factory) {
    return $factory->create();
  }
}
?>