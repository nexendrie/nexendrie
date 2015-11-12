<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Parent of all front presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nexendrie\BasePresenter {
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->template->isAdmin = $this->user->isAllowed("site", "manage");
  }
  
  /**
   * The user must be logged in to see a page
   * 
   * @return void
   */
  protected function requiresLogin() {
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
  protected function mustNotBeLoggedIn() {
    if($this->user->isLoggedIn()) {
      $this->flashMessage("Už jsi přihlášen.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * The user must not be banned to see a page
   * 
   * @return void
   */
  protected function mustNotBeBanned() {
    if($this->user->identity->banned) {
      $this->flashMessage("Ještě neskončil tvůj trest.");
      $this->redirect("Prison:");
    }
  }
  
  /**
   * The user must not be on adventure to see a page
   * 
   * @return void
   */
  protected function mustNotBeTavelling() {
    if($this->user->isLoggedIn() AND $this->user->identity->travelling) {
      $this->flashMessage("Toto nemůžet dělat, když jsi na cestách.");
      $this->redirect("Homepage:");
    }
  }
}
?>