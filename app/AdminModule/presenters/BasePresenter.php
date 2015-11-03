<?php
namespace Nexendrie\AdminModule\Presenters;

/**
 * Parent of all admin presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nexendrie\BasePresenter {
  /**
   * Check if the user is logged in and if he/she can enter administration
   * 
   * @return void
   */
  protected function startup() {
    parent::startup();
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("Pro přístup do administrace musíš být přihlášený.");
      $this->redirect("Front:User:login");
    }
    if(!$this->user->isAllowed("site", "manage")) {
      $this->flashMessage("Nemáš přístup do administrace.");
      $this->redirect("Front:Homepage:");
    }
  }
}
?>