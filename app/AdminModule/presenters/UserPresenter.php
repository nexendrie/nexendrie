<?php
namespace Nexendrie\AdminModule\Presenters;

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
  
}
?>