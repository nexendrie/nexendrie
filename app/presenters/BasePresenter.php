<?php
namespace Nexendrie\Presenters;

/**
 * Parent of all presenters
 *
 * @author Jakub Konečný
 */
class BasePresenter extends \Nette\Application\UI\Presenter {
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    if($this->user->identity) $this->template->style = $this->user->identity->style;
  }
  
  /**
   * The user must be logged in to see a page
   * 
   * @return void
   */
  function requiresLogin() {
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("K zobrazení této stránky musíš být přihlášen.");
      $this->redirect("User:login");
    }
  }
  
  /**
   * The user must not be logged in to see a page
   * 
   * @return void
   */
  function mustNotBeLoggedIn() {
    if($this->user->isLoggedIn()) {
      $this->flashMessage("Už jsi přihlášen.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * The user must have specified rights to see a page
   * 
   * @param string $resource
   * @param string $action
   * @return void
   */
  function requiresPermissions($resource, $action) {
    if(!$this->user->isAllowed($resource, $action)) {
      $this->flashMessage("K zobrazení této stránky nemáš práva.");
      $this->redirect("Homepage:");
    }
  }
}
?>