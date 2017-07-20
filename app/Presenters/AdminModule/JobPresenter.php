<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditJobFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\Job as JobEntity,
    Nexendrie\Model\JobNotFoundException,
    Nextras\Orm\Entity\IEntity;

/**
 * Presenter Job
 *
 * @author Jakub Konečný
 */
class JobPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Job @autowire */
  protected $model;
  /** @var JobEntity */
  private $job;
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->job = $this->model->getJob($id);
    } catch(JobNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  public function actionAdd(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddJobForm(AddEditJobFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->addJob($values);
      $this->flashMessage("Práce přidána.");
      $this->redirect("Content:jobs");
    };
    return $form;
  }
  
  protected function createComponentEditJobForm(AddEditJobFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->job->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editJob($this->getParameter("id"), $values);
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:jobs");
    };
    return $form;
  }
}
?>