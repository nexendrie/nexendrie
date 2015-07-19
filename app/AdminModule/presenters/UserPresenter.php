<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Model;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
class UserPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\UserManager */
  protected $model;
  /** @var \Nexendrie\Model\Group */
  protected $groupModel;
  
  function __construct(Model\UserManager $model, Model\Group $groupModel) {
    $this->model = $model;
    $this->groupModel = $groupModel;
  }
  
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