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
  
  public function renderDefault(): void {
    $this->requiresPermissions("user", "list");
    $this->template->users = $this->model->listOfUsers();
  }
  
  public function actionEdit(int $id): void {
    $this->requiresPermissions("user", "edit");
    try {
      $this->model->get($id);
    } catch(UserNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  protected function createComponentEditUser(EditUserFormFactory $factory): Form {
    $form = $factory->create((int) $this->getParameter("id"));
    $form->onSuccess[] = function(\Nette\Application\UI\Form $form) {
      $this->flashMessage("Změny uloženy.");
      $this->redirect("default");
    };
    return $form;
  }
  
  public function actionBan(int $id): void {
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
  
  protected function createComponentBanUserForm(BanUserFormFactory $factory): Form {
    $form = $factory->create($this->getParameter("id"));
    $form->onSuccess[] = function(\Nette\Application\UI\Form $form) {
      $this->flashMessage("Uživatel uvězněn.");
    };
    return $form;
  }
}
?>