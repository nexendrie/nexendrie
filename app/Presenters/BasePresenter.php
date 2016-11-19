<?php
declare(strict_types=1);

namespace Nexendrie\Presenters;

/**
 * Ultimate ancestor of all presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  use \Kdyby\Autowired\AutowireProperties;
  use \Kdyby\Autowired\AutowireComponentFactories;
  
  /** @var \Nexendrie\Model\SettingsRepository @autowire */
  protected $sr;
  
  /**
   * Set website's style
   * 
   * @return void
   */
  protected function startup() {
    parent::startup();
    if($this->user->isLoggedIn()) {
      $this->template->style = $this->user->identity->style;
    }
  }
  
  function beforeRender() {
    $this->template->versionSuffix = $this->sr->settings["site"]["versionSuffix"];
  }
  
  /**
   * The user must have specified rights to see a page
   * 
   * @param string $resource
   * @param string $action
   * @return void
   */
  protected function requiresPermissions(string $resource, string $action) {
    if(!$this->user->isAllowed($resource, $action)) {
      $this->flashMessage("K zobrazení této stránky nemáš práva.");
      $this->redirect("Homepage:");
    }
  }
}
?>