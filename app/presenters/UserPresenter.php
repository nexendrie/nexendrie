<?php
namespace Nexendrie\Presenters;

use Nette\Application\UI;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
class UserPresenter extends \Nette\Application\UI\Presenter {
  /**
   * Creates form for logging in
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentLoginForm() {
    $form = new UI\Form;
    $form->addText("username", "Uživatelské jméno:")
      ->setRequired("Zadej jméno");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo");
    $form->addSubmit("login", "Přihlásit se");
    $form->onSuccess[] = array($this, "loginFormSucceeded");
    return $form;
  }
  
  /**
   * @todo return to previous page if possible, handle exceptions
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function loginFormSucceeded(UI\Form $form, $values) {
    $this->user->login($values["username"], $values["password"]);
    if($this->user->isLoggedIn()) {
      $this->flashMessage("Byl jsi úspěšně přihlášen.");
      $this->redirect("Homepage:");
    }
  }
  
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