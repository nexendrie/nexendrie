<?php
namespace Nexendrie\FrontModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\LoginFormFactory,
    Nexendrie\Forms\RegisterFormFactory,
    Nexendrie\Forms\UserSettingsFormFactory;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
class UserPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\UserManager @autowire */
  protected $model;
  
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
   * @param LoginFormFactory $factory
   * @return \Nette\Application\UI\Form
   */
  protected function createComponentLoginForm(LoginFormFactory $factory) {
    $form = $factory->create();
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
  function loginFormSucceeded(Form $form, $values) {
    $this->flashMessage("Byl jsi úspěšně přihlášen.");
    $this->redirect("Homepage:");
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
   * @param RegisterFormFactory $factory
   * @return Form
   */
  protected function createComponentRegisterForm(RegisterFormFactory $factory) {
    $form = $factory->create();
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
  function registerFormSucceeded(Form $form, $values) {
    $this->flashMessage("Registrace úspěšně proběhla. Můžeš se přihlásit.");
    $this->redirect("Homepage:");
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
  protected function createComponentUserSettingsForm(UserSettingsFormFactory $factory) {
    $form = $factory->create();
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
  function userSettingsFormSucceeded(Form $form, $values) {
    $this->flashMessage("Změny uloženy.");
    if($this->user->identity->style != $values["style"]) {
      $this->user->identity->style = $values["style"];
      $this->redirect("this");
    }
  }
}
?>