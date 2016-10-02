<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form,
    Nexendrie\Forms\LoginFormFactory,
    Nexendrie\Forms\RegisterFormFactory,
    Nexendrie\Forms\UserSettingsFormFactory,
    Nexendrie\Orm\User as UserEntity;

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
   * @param LoginFormFactory $factory
   * @return Form
   */
  protected function createComponentLoginForm(LoginFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, $values) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Byla jsi úspěšně přihlášena.";
      else $message = "Byl jsi úspěšně přihlášen.";
      $this->flashMessage($message);
      if($this->user->identity->banned) {
        if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Stále jsi uvězněná.";
        else $message = "Stále jsi uvězněný.";
        $this->flashMessage($message);
      }
      if($this->user->identity->travelling) $this->flashMessage("Stále jsi na dobrodružství.");
      $this->redirect("Homepage:");
    };
    return $form;
  }
  
  /**
   * @todo return to previous page if possible
   * @return void
   */
  function actionLogout() {
    if($this->user->isLoggedIn()) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Byla jsi úspěšně odhlášena.";
      else $message = "Byl jsi úspěšně odhlášen.";
      $this->flashMessage($message);
      $this->user->logout();
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
  function actionSettings() {
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