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
   * @todo return to previous page if possible
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function loginFormSucceeded(UI\Form $form, $values) {
    try {
      $this->user->login($values["username"], $values["password"]);
      $this->flashMessage("Byl jsi úspěšně přihlášen.");
      $this->redirect("Homepage:");
    } catch(\Nette\Security\AuthenticationException $e) {
      if($e->getCode() === \Nette\Security\IAuthenticator::IDENTITY_NOT_FOUND) {
        $form->addError("Neplatné uživatelské jméno.");
      }
      if($e->getCode() === \Nette\Security\IAuthenticator::INVALID_CREDENTIAL) {
        $form->addError("Neplatné heslo.");
      }
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
  
  /**
   * @return void
   */
  function actionRegister() {
    if($this->user->isLoggedIn()) {
      $this->flashMessage("Už jsi přihlášen.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * Creates form for registering
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentRegisterForm() {
    $form = new UI\Form;
    $form->addText("username", "Uživatelské jméno:")
      ->addRule(UI\Form::MAX_LENGTH, "Uživatelské jméno může mít maximálně 25 znaků." , 25)
      ->setRequired("Zadej jméno");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo");
    $form->addText("email", "E-mail:")
      ->addRule(UI\Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.");
    $form->addSubmit("login", "Přihlásit se");
    $form->onSuccess[] = array($this, "registerFormSucceeded");
    return $form;
  }
  
  /**
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function registerFormSucceeded(UI\Form $form, $values) {
    $model = $this->context->getService("model.user");
    try {
      $model->register($values);
      $this->flashMessage("Registrace úspěšně proběhla. Můžeš se přihlásit.");
      $this->redirect("Homepage:");
    } catch (\Nexendrie\RegistrationException $e) {
      if($e->getCode() === \Nexendrie\User::REG_DUPLICATE_USERNAME) {
        $form->addError("Zvolené uživatelské jméno je už zabráno.");
      }
      if($e->getCode() === \Nexendrie\User::REG_DUPLICATE_EMAIL) {
        $form->addError("Zadaný e-mail je už používán.");
      }
    }
  }
}
?>