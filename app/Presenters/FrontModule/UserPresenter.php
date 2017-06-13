<?php
declare(strict_types=1);

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
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  
  /**
   * Do not allow access login page if the user is already logged in
   * 
   * @return void
   */
  function actionLogin(): void {
    $this->mustNotBeLoggedIn();
  }
  
  /**
   * @param LoginFormFactory $factory
   * @return Form
   */
  protected function createComponentLoginForm(LoginFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, $values) {
      $message = $this->localeModel->genderMessage("Byl(a) jsi úspěšně přihlášen(a).");
      $this->flashMessage($message);
      if($this->user->identity->banned) {
        $message = $this->localeModel->genderMessage("Stále jsi uvězněn(ý|á).");
        $this->flashMessage($message);
      }
      if($this->user->identity->travelling) {
        $this->flashMessage("Stále jsi na dobrodružství.");
      }
      $this->redirect("Homepage:");
    };
    return $form;
  }
  
  /**
   * @todo return to previous page if possible
   * @return void
   */
  function actionLogout(): void {
    $message = "Nejsi přihlášen.";
    if($this->user->isLoggedIn()) {
      $message = $this->localeModel->genderMessage("Byl(a) jsi úspěšně odhlášen(a).");
      $this->user->logout();
    }
    $this->flashMessage($message);
    $this->redirect("Homepage:");
  }
  
  /**
   * Prevent registration when logged in
   * 
   * @return void
   */
  function actionRegister(): void {
    $this->mustNotBeLoggedIn();
  }
  
  /**
   * @param RegisterFormFactory $factory
   * @return Form
   */
  protected function createComponentRegisterForm(RegisterFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->flashMessage("Registrace úspěšně proběhla. Můžeš se přihlásit.");
      $this->redirect("Homepage:");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function actionSettings(): void {
    $this->requiresLogin();
  }
  
  /**
   * @param UserSettingsFormFactory $factory
   * @return Form
   */
  protected function createComponentUserSettingsForm(UserSettingsFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, $values) {
      $this->model->refreshIdentity();
      $this->flashMessage("Změny uloženy.");
      $this->redirect("this");
    };
    return $form;
  }
}
?>