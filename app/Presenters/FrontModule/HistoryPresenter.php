<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IHistoryControlFactory,
    Nexendrie\Components\HistoryControl;

/**
 * Presenter History
 *
 * @author Jakub Konečný
 */
class HistoryPresenter extends BasePresenter {
  function renderDefault(string $page = "index"): void {
    $this->template->page = $page;
  }
  
  protected function createComponentHistory(IHistoryControlFactory $factory): HistoryControl {
    return $factory->create();
  }
}
?>