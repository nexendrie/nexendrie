<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\EditUserFormFactory,
    Nexendrie\Forms\BanUserFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\UserNotFoundException;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
class UserPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\UserManager @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Group @autowire */
  protected $groupModel;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->requiresPermissions("user", "list");
    $this->template->users = $this->model->listOfUsers();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit(int $id) {
    $this->requiresPermissions("user", "edit");
  }
  
  /**
   * @param EditUserFormFactory $factory
   * @return Form
   */
  protected function createComponentEditUser(EditUserFormFactory $factory): Form {
    $form = $factory->create((int) $this->getParameter("id"));
    $form->onSuccess[] = function(\Nette\Application\UI\Form $form) {
      $this->flashMessage("Změny uloženy.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionBan(int $id) {
    $this->requiresPermissions("user", "ban");
    if($id == 0) {
      $this->flashMessage("Neoprávněná operace.");
      $this->redirect("default");
    }
    try {
      $user = $this->model->get($id);
      if($user->group->id === 1) {
        $this->flashMessage("Neoprávněná operace.");
        $this->redirect("default");
      } elseif($user->banned) {
        $this->flashMessage("Uživatel už je uvězněn.");
        $this->redirect("default");
      }
    } catch(UserNotFoundException $e) {
      $this->flashMessage("Zadaný uživatel neexistuje.");
      $this->redirect("default");
    }
    $this->template->name = $user->publicname;
  }
  
  /**
   * @param BanUserFormFactory $factory
   * @return Form
   */
  protected function createComponentBanUserForm(BanUserFormFactory $factory): Form {
    $form = $factory->create($this->getParameter("id"));
    $form->onSuccess[] = function(\Nette\Application\UI\Form $form) {
      $this->flashMessage("Uživatel uvězněn.");
    };
    return $form;
  }
}
?>