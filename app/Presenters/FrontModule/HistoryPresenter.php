<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IHistoryControlFactory;
use Nexendrie\Components\HistoryControl;

/**
 * Presenter History
 *
 * @author Jakub Konečný
 */
final class HistoryPresenter extends BasePresenter {
  protected bool $cachingEnabled = false;

  public function renderDefault(string $page = "index"): void {
    $this->template->page = $page;
  }
  
  protected function createComponentHistory(IHistoryControlFactory $factory): HistoryControl {
    return $factory->create();
  }
}
?>