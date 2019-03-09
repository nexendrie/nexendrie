<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditJobFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Orm\Job as JobEntity;
use Nexendrie\Model\JobNotFoundException;

/**
 * Presenter Job
 *
 * @author Jakub Konečný
 */
final class JobPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Job */
  protected $model;
  /** @var JobEntity */
  private $job;
  
  public function __construct(\Nexendrie\Model\Job $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->job = $this->model->getJob($id);
    } catch(JobNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddJobForm(AddEditJobFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Práce přidána.");
      $this->redirect("Content:jobs");
    };
    return $form;
  }
  
  protected function createComponentEditJobForm(AddEditJobFormFactory $factory): Form {
    $form = $factory->create($this->job);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:jobs");
    };
    return $form;
  }
}
?>