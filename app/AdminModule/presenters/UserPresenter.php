<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Forms\EditUserFormFactory;

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
  function actionEdit($id) {
    $this->requiresPermissions("user", "edit");
  }
  
  protected function createComponentEditUser(EditUserFormFactory $factory) {
    $form = $factory->create($this->getParameter("id"));
    $form->onSuccess[] = function(\Nette\Application\UI\Form $form) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>