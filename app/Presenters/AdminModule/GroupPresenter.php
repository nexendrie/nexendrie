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
  /** @var \Nexendrie\Model\Group */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Group $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function renderDefault(): void {
    $this->requiresPermissions("group", "list");
    $groups = $this->model->listOfGroups();
    $this->template->groups = $groups;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("group", "edit");
    if(!$this->model->exists($id)) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  protected function createComponentEditGroupForm(EditGroupFormFactory $factory): Form {
    /** @var \Nexendrie\Orm\Group $group */
    $group = $this->model->ormGet((int) $this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->user = $this->user;
      $this->model->edit((int) $this->getParameter("id"), $values);
      $this->flashMessage("Skupina upravena.");
      $this->redirect("default");
    };
    $form->setDefaults($group->toArray());
    return $form;
  }
  
  public function actionMembers(int $id): void {
    $this->requiresPermissions("group", "list");
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderMembers(int $id): void {
    $group = $this->model->ormGet($id);
    if(is_null($group)) {
      throw new \Nette\Application\BadRequestException;
    }
    $this->template->group = $group;
  }
}
?>