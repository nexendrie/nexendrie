<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI\Form;

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
    if(!$this->model->exists($id)) $this->forward("notfound");
  }
  
  /**
   * Creates form for editting group
   * 
   * @return \Nette\Application\UI\Form
   */
  function createComponentEditGroupForm() {
    $group = $this->model->get($this->getParameter("id"));
    $form = new Form;
    $form->addText("name", "Jméno:")
      ->addRule(Form::MAX_LENGTH, "Jméno skupiny může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej jméno skupiny.");
    $form->addText("singleName", "Titul člena:")
      ->addRule(Form::MAX_LENGTH, "Titul člena může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titul člena.")
      ->setDefaultValue($group->single_name);
    $form->addText("level", "Úroveň skpuiny:")
      ->addRule(Form::INTEGER, "Úroveň skupiny musí být číslo")
      ->addRule(Form::MAX_LENGTH, "Úroveň skupiny může mít maximálně 5 znaků.", 5)
      ->setRequired("Zadej úroveň skupiny.");
    $form->addSubmit("send", "Odeslat");
    $form->onSuccess[] = array($this, "editGroupFormSucceeded");
    $form->setDefaults((array) $group);
    return $form;
  }
  
  /**
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   * @return void
   */
  function editGroupFormSucceeded(Form $form, $values) {
    $this->model->user = $this->context->getService("security.user");
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