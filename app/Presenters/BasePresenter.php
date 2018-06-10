<?php
declare(strict_types=1);

namespace Nexendrie\Presenters;

use Nexendrie\Menu\IMenuControlFactory;
use Nexendrie\Menu\MenuControl;

/**
 * Ultimate ancestor of all presenters
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  use \Kdyby\Autowired\AutowireComponentFactories;
  
  /** @var \Nexendrie\Model\SettingsRepository */
  protected $sr;
  
  public function injectSettingsRepository(\Nexendrie\Model\SettingsRepository $sr): void {
    $this->sr = $sr;
  }
  
  /**
   * Set website's style
   */
  protected function startup(): void {
    parent::startup();
    $this->template->style = $this->sr->settings["newUser"]["style"];
    if($this->user->isLoggedIn()) {
      $this->template->style = $this->user->identity->style;
    }
  }
  
  protected function beforeRender(): void {
    $this->template->versionSuffix = $this->sr->settings["site"]["versionSuffix"];
  }
  
  /**
   * The user must have specified rights to see a page
   */
  protected function requiresPermissions(string $resource, string $action): void {
    if(!$this->user->isAllowed($resource, $action)) {
      $this->flashMessage("K zobrazení této stránky nemáš práva.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentMenu(IMenuControlFactory $factory): MenuControl {
    return $factory->create();
  }
}
?>