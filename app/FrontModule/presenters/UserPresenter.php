<?php
namespace Nexendrie\Presenters\FrontModule;

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
    $form->onSuccess[] = function(Form $form, $values) {
      $this->flashMessage("Byl jsi úspěšně přihlášen.");
      if($this->user->identity->banned) $this->flashMessage("Stále jsi uvězněný.");
      $this->redirect("Homepage:");
    };
    return $form;
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
    $form->onSuccess[] = function(Form $form, $values) {
      $this->flashMessage("Registrace úspěšně proběhla. Můžeš se přihlásit.");
      $this->redirect("Homepage:");
    };
    return $form;
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
    $form->onSuccess[] = function(Form $form, $values) {
      $this->flashMessage("Změny uloženy.");
      if($this->user->identity->style != $values["style"]) {
        $this->user->identity->style = $values["style"];
        $this->redirect("this");
      }
    };
    return $form;
  }
}
?>