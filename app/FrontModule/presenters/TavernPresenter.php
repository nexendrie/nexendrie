<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Components\TavernControlFactory,
    Nexendrie\Components\TavernControl;

/**
 * Presenter Tavern
 *
 * @author Jakub Konečný
 */
class TavernPresenter extends BasePresenter {
  /**
   * @param TavernControlFactory $factory
   * @return TavernControl
   */
  protected function createComponentTavern(TavernControlFactory $factory) {
    return $factory->create();
  }
}
?>