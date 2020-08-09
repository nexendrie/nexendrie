<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\LoginFormFactory;
use Nexendrie\Forms\RegisterFormFactory;
use Nexendrie\Forms\UserSettingsFormFactory;
use Nexendrie\Orm\Model as ORM;
use Nextras\Orm\Collection\ICollection;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
final class UserPresenter extends BasePresenter {
  protected \Nexendrie\Model\Authenticator $model;
  protected \Nexendrie\Model\Locale $localeModel;
  protected ORM $orm;
  /** @persistent */
  public string $backlink = "";
  protected bool $cachingEnabled = false;
  
  public function __construct(\Nexendrie\Model\Authenticator $model, \Nexendrie\Model\Locale $localeModel, ORM $orm) {
    parent::__construct();
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->orm = $orm;
  }
  
  /**
   * Do not allow access login page if the user is already logged in
   */
  public function actionLogin(): void {
    $this->mustNotBeLoggedIn();
  }
  
  protected function createComponentLoginForm(LoginFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $message = $this->localeModel->genderMessage("Byl(a) jsi úspěšně přihlášen(a).");
      $this->flashMessage($message);
      if($this->user->identity->banned) {
        $message = $this->localeModel->genderMessage("Stále jsi uvězněn(ý|á).");
        $this->flashMessage($message);
      }
      if($this->user->identity->travelling) {
        $this->flashMessage("Stále jsi na dobrodružství.");
      }
      $this->restoreRequest($this->backlink);
      $this->redirect("Homepage:");
    };
    return $form;
  }

  public function actionLogout(): void {
    $message = "Nejsi přihlášen.";
    if($this->user->isLoggedIn()) {
      $message = $this->localeModel->genderMessage("Byl(a) jsi úspěšně odhlášen(a).");
      $this->user->logout(true);
    }
    $this->flashMessage($message);
    $this->redirect("Homepage:");
  }
  
  /**
   * Prevent registration when logged in
   */
  public function actionRegister(): void {
    $this->mustNotBeLoggedIn();
  }
  
  protected function createComponentRegisterForm(RegisterFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Registrace úspěšně proběhla. Můžeš se přihlásit.");
      $this->redirect("Homepage:");
    };
    return $form;
  }
  
  public function actionSettings(): void {
    $this->requiresLogin();
  }
  
  protected function createComponentUserSettingsForm(UserSettingsFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->model->user = $this->user;
      $this->model->refreshIdentity();
      $this->flashMessage("Změny uloženy.");
      $this->redirect("this");
    };
    return $form;
  }

  public function renderList(): void {
    $this->template->users = $this->orm->users->findAll()
      ->orderBy("this->group->level", ICollection::DESC)
      ->orderBy("created");
  }
}
?>