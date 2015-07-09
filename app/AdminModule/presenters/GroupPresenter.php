<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI;

/**
 * Presenter Group
 *
 * @author Jakub Konečný
 */
class GroupPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Group */
  protected $model;
  
  /**
   * @param \Nexendrie\Model\Group $model
   */
  function __construct(\Nexendrie\Model\Group $model) {
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
   * Creates form for editting group
   * 
   * @return \Nette\Application\UI\Form
   */
  function createComponentEditGroupForm() {
    $group = $this->model->get($this->getParameter("id"));
    $form = new UI\Form;
    $form->addText("name", "Jméno:")
      ->addRule(UI\Form::MAX_LENGTH, "Jméno skupiny může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej jméno skupiny.");
    $form->addText("single_name", "Titul člena:")
      ->addRule(UI\Form::MAX_LENGTH, "Titul člena může mít maximálně 30 znaků.", 30)
      ->setRequired("Zadej titul člena.");
    $form->addText("level", "Úroveň skpuiny:")
      ->addRule(UI\Form::INTEGER, "Úroveň skupiny musí být číslo")
      ->addRule(UI\Form::MAX_LENGTH, "Úroveň skupiny může mít maximálně 5 znaků.", 5)
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
  function editGroupFormSucceeded(UI\Form $form, $values) {
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
}
?>