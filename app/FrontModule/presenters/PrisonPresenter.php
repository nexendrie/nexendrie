<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Prison
 *
 * @author Jakub Konečný
 */
class PrisonPresenter extends BasePresenter {
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    if(!$this->user->isLoggedIn()) $this->redirect("Homepage:");
    elseif(!$this->user->identity->banned) $this->redirect("Homepage:");
  }
}
?>