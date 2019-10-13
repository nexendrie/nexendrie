<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\ITavernControlFactory;
use Nexendrie\Components\TavernControl;

/**
 * Presenter Tavern
 *
 * @author Jakub Konečný
 */
final class TavernPresenter extends BasePresenter {
  /** @var bool */
  protected $cachingEnabled = false;

  protected function startup(): void {
    parent::startup();
    $this->mustNotBeTavelling();
  }
  
  protected function createComponentTavern(ITavernControlFactory $factory): TavernControl {
    return $factory->create();
  }
}
?>