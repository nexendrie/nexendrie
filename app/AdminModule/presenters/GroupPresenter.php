<?php
namespace Nexendrie\AdminModule\Presenters;

/**
 * Presenter Group
 *
 * @author Jakub Konečný
 */
class GroupPresenter extends BasePresenter {
  /** @var \Nexendrie\Group */
  protected $model;
  
  /**
   * @param \Nexendrie\Group $model
   */
  function __construct(\Nexendrie\Group $model) {
    $this->model = $model;
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->requiresPermissions("group", "list");
    $groups = $this->model->listOfGroups();
    foreach($groups as $group) {
      $group->members = $this->model->numberOfMembers($group->id);
    }
    $this->template->groups = $groups;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("group", "edit");
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionMembers($id) {
    $this->requiresPermissions("group", "list");
  }
}
?>