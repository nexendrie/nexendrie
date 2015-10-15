<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Forms\AddEditJobFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\Job as JobEntity,
    Nexendrie\Model\JobNotFoundException;

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
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    try {
      $this->job = $this->model->getJob($id);
    } catch(JobNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param AddEditJobFormFactory $factory
   * @return Form
   */
  protected function createComponentAddJobForm(AddEditJobFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->addJob($form->getValues(true));
      $this->flashMessage("Práce přidána.");
      $this->redirect("Content:jobs");
    };
    return $form;
  }
  
  /**
   * @param AddEditJobFormFactory $factory
   * @return Form
   */
  protected function createComponentEditJobForm(AddEditJobFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->job->dummyArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->editJob($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>