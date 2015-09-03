<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\EditGroupFormFactory;

/**
 * Presenter Group
 *
 * @author Jakub Konečný
 */
class GroupPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Group @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->requiresPermissions("group", "list");
    $groups = $this->model->listOfGroups();
    $this->template->groups = $groups;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("group", "edit");
    if(!$this->model->exists($id)) $this->forward("notfound");
  }
  
  /**
   * Creates form for editting group
   * 
   * @param EditGroupFormFactory $factory
   * @return \Nette\Application\UI\Form
   */
  function createComponentEditGroupForm(EditGroupFormFactory $factory) {
    $group = $this->model->ormGet($this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = array($this, "editGroupFormSucceeded");
    $form->setDefaults($group->toArray());
    return $form;
  }
  
  /**
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   * @return void
   */
  function editGroupFormSucceeded(Form $form, $values) {
    $this->model->user = $this->user;
    $this->model->edit($this->getParameter("id"), $values);
    $this->flashMessage("Skupina upravena.");
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionMembers($id) {
    $this->requiresPermissions("group", "list");
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderMembers($id) {
    $group = $this->model->ormGet($id);
    if(!$group) $this->forward("notfound");
    else $this->template->group = $group;
  }
}
?>