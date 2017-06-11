<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

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
  function renderDefault(): void {
    $this->requiresPermissions("group", "list");
    $groups = $this->model->listOfGroups();
    $this->template->groups = $groups;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id): void {
    $this->requiresPermissions("group", "edit");
    if(!$this->model->exists($id)) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param EditGroupFormFactory $factory
   * @return Form
   */
  protected function createComponentEditGroupForm(EditGroupFormFactory $factory): Form {
    $group = $this->model->ormGet((int) $this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->user = $this->user;
      $this->model->edit($this->getParameter("id"), $values);
      $this->flashMessage("Skupina upravena.");
      $this->redirect("default");
    };
    $form->setDefaults($group->toArray());
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionMembers(int $id): void {
    $this->requiresPermissions("group", "list");
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function renderMembers(int $id): void {
    $group = $this->model->ormGet($id);
    if(is_null($group)) {
      throw new \Nette\Application\BadRequestException;
    }
    $this->template->group = $group;
  }
}
?>