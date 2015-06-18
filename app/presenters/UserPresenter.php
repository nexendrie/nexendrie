<?php
namespace Nexendrie\Presenters;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
class UserPresenter extends \Nette\Application\UI\Presenter {
  /**
   * @todo return to previous page if possible
   * @return void
   */
  function actionLogout() {
    if($this->user->isLoggedIn()) {
      $this->user->logout();
      $this->flashMessage("Byl jsi úspěšně odhlášen.");
    } else {
      $this->flashMessage("Nejsi přihlášen.");
    }
    $this->redirect("Homepage:");
  }
}
?>