<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\JobNotFoundException;
use Nexendrie\Model\JobMessageNotFoundException;
use Nexendrie\Orm\JobMessage as JobMessageEntity;
use Nexendrie\Orm\Job as JobEntity;
use Nette\Application\UI\Form;
use Nexendrie\Forms\AddEditJobMessageFormFactory;
use Nextras\Orm\Entity\ToArrayConverter;

/**
 * Presenter JobMessages
 *
 * @author Jakub Konečný
 */
final class JobMessagesPresenter extends BasePresenter {
  protected \Nexendrie\Model\Job $model;
  private JobMessageEntity $message;
  private JobEntity $job;
  
  public function __construct(\Nexendrie\Model\Job $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function actionList(int $id): void {
    $this->requiresPermissions("content", "list");
    try {
      $this->template->messages = $this->model->listOfMessages($id);
      $this->template->jobId = $id;
    } catch(JobNotFoundException $e) {
      $this->forward("Job:notfound");
    }
  }
  
  public function actionAdd(int $id): void {
    $this->requiresPermissions("content", "add");
    try {
      $this->job = $this->model->getJob($id);
      $this->template->jobName = $this->job->name;
    } catch(JobNotFoundException $e) {
      $this->forward("Job:notfound");
    }
  }
  
  protected function createComponentAddJobMessageForm(AddEditJobMessageFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values): void {
      $values["job"] = $this->job->id;
      $this->model->addMessage($values);
      $this->flashMessage("Hláška přidána.");
      $this->redirect("list", ["id" => $this->job->id]);
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->message = $this->model->getMessage($id);
    } catch(JobMessageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditJobMessageForm(AddEditJobMessageFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->message->toArray(ToArrayConverter::RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values): void {
      $this->model->editMessage($this->message->id, $values);
      $this->flashMessage("Hláška upravena.");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionDelete(int $id): void {
    try {
      $job = $this->model->deleteMessage($id);
      $this->flashMessage("Hláška smazána.");
      $this->redirect("list", ["id" => $job]);
    } catch(JobMessageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
}
?>