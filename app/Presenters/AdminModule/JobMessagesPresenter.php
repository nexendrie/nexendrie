<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\JobNotFoundException,
    Nexendrie\Model\JobMessageNotFoundException,
    Nexendrie\Orm\JobMessage as JobMessageEntity,
    Nexendrie\Orm\Job as JobEntity,
    Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditJobMessageFormFactory,
    Nextras\Orm\Entity\IEntity;

/**
 * Presenter JobMessages
 *
 * @author Jakub Konečný
 */
class JobMessagesPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Job @autowire */
  protected $model;
  /** @var JobMessageEntity */
  private $message;
  /** @var JobEntity */
  private $job;
  
  /**
   * @param int $id
   * @return void
   */
  function actionList(int $id): void {
    $this->requiresPermissions("content", "list");
    try {
      $this->template->messages = $this->model->listOfMessages($id);
      $this->template->jobId = $id;
    } catch(JobNotFoundException $e) {
      $this->forward("Job:notfound");
    }
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionAdd(int $id): void {
    $this->requiresPermissions("content", "add");
    try {
      $this->job = $this->model->getJob($id);
      $this->template->jobName = $this->job->name;
    } catch(JobNotFoundException $e) {
      $this->forward("Job:notfound");
    }
  }
  
  /**
   * @param AddEditJobMessageFormFactory $factory
   * @return Form
   */
  protected function createComponentAddJobMessageForm(AddEditJobMessageFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $values["job"] = $this->job->id;
      $this->model->addMessage($values);
      $this->flashMessage("Hláška přidána.");
      $this->redirect("list", ["id" => $this->job->id]);
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->message = $this->model->getMessage($id);
    } catch(JobMessageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditJobMessageFormFactory $factory
   * @return Form
   */
  protected function createComponentEditJobMessageForm(AddEditJobMessageFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->message->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editMessage($this->message->id, $values);
      $this->flashMessage("Hláška upravena.");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionDelete(int $id): void {
    try {
      $job = $this->model->deleteMessage($id);
      $this->flashMessage("Hláška smazána.");
      $this->redirect("list", ["id" => $job]);
    } catch(JobMessageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>