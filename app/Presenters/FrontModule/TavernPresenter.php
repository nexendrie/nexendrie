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
final class TavernPresenter extends BasePresenter {
  protected function startup(): void {
    parent::startup();
    $this->mustNotBeTavelling();
  }
  
  protected function createComponentTavern(ITavernControlFactory $factory): TavernControl {
    return $factory->create();
  }
}
?>