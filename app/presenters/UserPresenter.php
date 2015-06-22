<?php
namespace Nexendrie\Presenters;

use Nette\Application\UI,
  \Nette\Utils\Finder,
  \Nette\Neon\Neon,
  \Nette\Utils\Arrays;

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
   * Gets list of styles
   * 
   * @return array
   */
  protected function getStylesList() {
    $styles = array();
    $dir = WWW_DIR . "/styles";
    $file = file_get_contents("$dir/list.neon");
    $list = Neon::decode($file);
    foreach(Finder::findFiles("*.css")->in($dir) as $style) {
      $key = $style->getBaseName(".css");
      $value = Arrays::get($list, $key, $key);
      $styles[$key] = $value;
    }
    return $styles;
  }
  
  /**
   * Creates form for changing user's settings
   * 
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentUserSettingsForm() {
    $this->model->user = $this->context->getService("security.user");
    $form = new UI\Form;
    $form->addGroup("Účet");
    $form->addText("publicname", "Zobrazované jméno:")
      ->addRule(UI\Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků." , 25)
      ->setRequired("Zadej jméno.");
    $form->addText("email", "E-mail:")
      ->addRule(UI\Form::EMAIL, "Zadej platný e-mail.")
      ->setRequired("Zadej e-mail.");
    $form->addRadioList("style", "Vzhled stránek:", $this->getStylesList());
    $form->addCheckbox("infomails", "Posílat informační e-maily");
    $form->addGroup("Heslo")
      ->setOption("description", "Současné a nové heslo vyplňujte jen pokud ho chcete změnit.");
    $form->addPassword("password_old", "Současné heslo:");
    $form->addPassword("password_new", "Nové heslo:");
    $form->addPassword("password_check", "Nové heslo (kontrola):");
    $form->currentGroup = NULL;
    $form->addSubmit("save", "Uložit změny");
    $form->onSuccess[] = array($this, "userSettingsFormSucceeded");
    $form->onValidate[] = array($this, "userSettingsFormValidate");
    $form->setDefaults($this->model->getSettings());
    return $form;
  }
  
  /**
   * @param \Nette\Application\UI\Form $form
   * @return void
   */
  function userSettingsFormValidate(UI\Form $form) {
    $values = $form->getValues();
    if(empty($values["password_old"]) AND !empty($values["password_new"])) $form->addError("Musíš zadat současné heslo.");
    if($values["password_new"] != $values["password_check"]) $form->addError("Hesla se neshodují.");
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
      if($e->getCode() === \Nexendrie\UserManager::SET_INVALID_PASSWORD) {
        $form->addError("Neplatné heslo.");
      }
    }
  }
}
?>