<?php
namespace Nexendrie;

/**
 * Ultimate ancestor of all presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  use \Kdyby\Autowired\AutowireProperties;
  use \Kdyby\Autowired\AutowireComponentFactories;
  
  /**
   * Set website's style
   * 
   * @return void
   */
  function startup() {
    parent::startup();
    if($this->user->identity) $this->template->style = $this->user->identity->style;
  }
  
  /**
   * The user must have specified rights to see a page
   * 
   * @param string $resource
   * @param string $action
   * @return void
   */
  protected function requiresPermissions($resource, $action) {
    if(!$this->user->isAllowed($resource, $action)) {
      $this->flashMessage("K zobrazení této stránky nemáš práva.");
      $this->redirect("Homepage:");
    }
  }
}
?>