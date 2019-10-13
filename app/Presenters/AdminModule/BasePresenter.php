<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

/**
 * Parent of all admin presenters
 *
 * @author Jakub Konečný
 */
abstract class BasePresenter extends \Nexendrie\Presenters\BasePresenter {
  /** @var bool */
  protected $cachingEnabled = false;

  /**
   * Check if the user is logged in and if he/she can enter administration
   */
  protected function startup(): void {
    parent::startup();
    if(!$this->user->isLoggedIn()) {
      $this->flashMessage("Pro přístup do administrace musíš být přihlášený.");
      $this->redirect(":Front:User:login", ["backlink" => $this->storeRequest()]);
    }
    if(!$this->user->isAllowed("site", "manage")) {
      $this->flashMessage("Nemáš přístup do administrace.");
      $this->redirect(":Front:Homepage:");
    }
  }
}
?>