<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IHelpControlFactory;
use Nexendrie\Components\HelpControl;

/**
 * Presenter Help
 *
 * @author Jakub Konečný
 */
final class HelpPresenter extends BasePresenter {
  protected bool $cachingEnabled = false;

  public function renderDefault(string $page = "index"): void {
    $this->template->page = $page;
  }
  
  protected function createComponentHelp(IHelpControlFactory $factory): HelpControl {
    return $factory->create();
  }
}
?>