<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Model\JobNotFoundException,
    Nexendrie\Model\JobMessageNotFoundException,
    Nexendrie\Orm\JobMessage as JobMessageEntity,
    Nexendrie\Orm\Job as JobEntity,
    Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditJobMessageFormFactory;

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
  function actionList($id) {
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
  function actionAdd($id) {
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
  protected function createComponentAddJobMessageForm(AddEditJobMessageFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $data = $form->getValues(true);
      $data["job"] = $this->job->id;
      $this->model->addMessage($data);
      $this->flashMessage("Hláška přidána.");
      $this->redirect("list", array("id" => $this->job->id));
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->message = $this->model->getMessage($id);
    } catch(JobMessageNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param AddEditJobMessageFormFactory $factory
   * @return Form
   */
  protected function createComponentEditJobMessageForm(AddEditJobMessageFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->message->dummyArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->editMessage($this->message->id, $form->getValues(true));
      $this->flashMessage("Hláška upravena.");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionDelete($id) {
    try {
      $job = $this->model->deleteMessage($id);
      $this->flashMessage("Hláška smazána.");
      $this->redirect("list", array("id" => $job));
    } catch(JobMessageNotFoundException $e) {
      $this->forward("notfound");
    }
  }
}
?>