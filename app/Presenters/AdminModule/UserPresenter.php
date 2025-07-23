<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\EditUserFormFactory;
use Nexendrie\Forms\BanUserFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\UserManager;
use Nexendrie\Model\UserNotFoundException;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
final class UserPresenter extends BasePresenter {
  public function __construct(private readonly UserManager $model) {
    parent::__construct();
  }
  
  public function renderDefault(): void {
    $this->requiresPermissions("user", "list");
    $this->template->users = $this->model->listOfUsers();
  }

  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("user", "edit");
    try {
      $this->model->get($id);
    } catch(UserNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditUser(EditUserFormFactory $factory): Form {
    $form = $factory->create((int) $this->getParameter("id"));
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Změny uloženy.");
      $this->redirect("default");
    };
    return $form;
  }
  
  public function actionBan(int $id): void {
    $this->requiresPermissions("user", "ban");
    if($id === 0) {
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
    $form = $factory->create((int) $this->getParameter("id"));
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Uživatel uvězněn.");
    };
    return $form;
  }
}
?>