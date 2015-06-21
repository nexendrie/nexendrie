<?php
namespace Nexendrie\Presenters;

use Nette\Application\UI;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
class UserPresenter extends BasePresenter {
  /** @var \Nexendrie\UserManager */
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->model = $this->context->getByType("\Nexendrie\UserManager");
  }
  
  /**
   * Do not allow access login page if the user is already logged in
   * 
   * @return void
   */
  function actionLogin() {
    $this->mustNotBeLoggedIn();
  }
  
  /**
   * Creates form for logging in
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentLoginForm() {
    $form = new UI\Form;
    $form->addText("username", "Uživatelské jméno:")
      ->setRequired("Zadej jméno.");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo.");
    $form->addSubmit("login", "Přihlásit se");
    $form->onSuccess[] = array($this, "loginFormSucceeded");
    return $form;
  }
  
  /**
   * Login the user
   * @todo return to previous page if possible
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   * @return void
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
   * Log out the user
   * 
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
   * Prevent registration when logged in
   * 
   * @return void
   */
  function actionRegister() {
    $this->mustNotBeLoggedIn();
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
      ->setRequired("Zadej jméno.");
    $form->addPassword("password", "Heslo:")
      ->setRequired("Zadej heslo.");
    $form->addText("email", "E-mail:")
      ->addRule(UI\Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.");
    $form->addSubmit("login", "Přihlásit se");
    $form->onSuccess[] = array($this, "registerFormSucceeded");
    return $form;
  }
  
  /**
   * Register new user
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   * @return void
   */
  function registerFormSucceeded(UI\Form $form, $values) {
    try {
      $this->model->register($values);
      $this->flashMessage("Registrace úspěšně proběhla. Můžeš se přihlásit.");
      $this->redirect("Homepage:");
    } catch (\Nexendrie\RegistrationException $e) {
      if($e->getCode() === \Nexendrie\UserManager::REG_DUPLICATE_USERNAME) {
        $form->addError("Zvolené uživatelské jméno je už zabráno.");
      }
      if($e->getCode() === \Nexendrie\UserManager::REG_DUPLICATE_EMAIL) {
        $form->addError("Zadaný e-mail je už používán.");
      }
    }
  }
  
  /**
   * @return void
   */
  function actionSettings() {
    $this->requiresLogin();
  }
  
  /**
   * Creates form for changing user's settings
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentUserSettingsForm() {
    $this->model->user = $this->context->getService("security.user");
    $settings = $this->model->getSettings();
    $form = new UI\Form;
    $form->addGroup("Účet");
    $form->addText("publicname", "Zobrazované jméno:")
      ->addRule(UI\Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků." , 25)
      ->setRequired("Zadej jméno.")
      ->setDefaultValue($settings->publicname);
    $form->addText("email", "E-mail:")
      ->addRule(UI\Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.")
      ->setDefaultValue($settings->email);
    $form->currentGroup = NULL;
    $form->addSubmit("save", "Uložit změny");
    $form->onSuccess[] = array($this, "userSettingsFormSucceeded");
    return $form;
  }
  
  /**
   * Change user's settings
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   * @return void
   */
  function userSettingsFormSucceeded(UI\Form $form, $values) {
    try {
      $this->model->user = $this->context->getService("security.user");
      $this->model->changeSettings($values);
      $this->flashMessage("Změny uloženy.");
    } catch (\Nexendrie\SettingsException $e) {
      if($e->getCode() === \Nexendrie\UserManager::REG_DUPLICATE_USERNAME) {
        $form->addError("Zvolené jméno je už zabráno.");
      }
      if($e->getCode() === \Nexendrie\UserManager::REG_DUPLICATE_EMAIL) {
        $form->addError("Zadaný e-mail je už používán.");
      }
    }
  }
}
?>