<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Components\HelpControlFactory,
    Nexendrie\Components\HelpControl;

/**
 * Presenter Help
 *
 * @author Jakub Konečný
 */
class HelpPresenter extends BasePresenter {
  function renderDefault($page) {
    $this->template->page = $page;
  }
  
  /**
   * @param HelpControlFactory $factory
   * @return HelpControl
   */
  protected function createComponentHelp(HelpControlFactory $factory) {
    return $factory->create();
  }
}
?>