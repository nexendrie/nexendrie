<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Parent of all front presenters
 *
 * @author Jakub Konečný
 */
class BasePresenter extends \Nexendrie\BasePresenter {
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->template->isAdmin = $this->user->isAllowed("site", "manage");
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
}
?>