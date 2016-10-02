<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\HelpControlFactory,
    Nexendrie\Components\HelpControl;

/**
 * Presenter Help
 *
 * @author Jakub Konečný
 */
class HelpPresenter extends BasePresenter {
  /**
   * @param string $page
   * @return void
   */
  function renderDefault(string $page = "index") {
    $this->template->page = $page;
  }
  
  /**
   * @param HelpControlFactory $factory
   * @return HelpControl
   */
  protected function createComponentHelp(HelpControlFactory $factory): HelpControl {
    return $factory->create();
  }
}
?>