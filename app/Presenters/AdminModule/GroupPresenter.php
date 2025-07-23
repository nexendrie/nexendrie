<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\EditGroupFormFactory;
use Nexendrie\Model\Group;

/**
 * Presenter Group
 *
 * @author Jakub Konečný
 */
final class GroupPresenter extends BasePresenter {
  public function __construct(private readonly Group $model) {
    parent::__construct();
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
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditGroupForm(EditGroupFormFactory $factory): Form {
    /** @var \Nexendrie\Orm\Group $group */
    $group = $this->model->ormGet((int) $this->getParameter("id"));
    $form = $factory->create($group);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Skupina upravena.");
      $this->redirect("default");
    };
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
    if($group === null) {
      throw new \Nette\Application\BadRequestException();
    }
    $this->template->group = $group;
  }
}
?>