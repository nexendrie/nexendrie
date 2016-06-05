<?php
namespace Nexendrie\Presenters\AdminModule;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditPollFormFactory;

/**
 * Presenter Polls
 *
 * @author Jakub Konečný
 */
class PollsPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Polls @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->polls = $this->model->all();
  }
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("poll", "add");
  }
  
  /**
   * Creates form for adding poll
   * 
   * @param AddEditPollFormFactory $factory
   * @return Form
   */
  protected function createComponentAddPollForm(AddEditPollFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->user = $this->user;
      $this->model->add($form->getValues(true));
      $this->flashMessage("Anketa přidána.");
      $this->redirect("Polls:");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("poll", "add");
    if(!$this->model->exists($id)) throw new \Nette\Application\BadRequestException;
  }
  
  /**
   * Creates form for editing poll
   * 
   * @param AddEditPollFormFactory $factory
   * @return Form
   */
  protected function createComponentEditPollForm(AddEditPollFormFactory $factory) {
    $poll = $this->model->view($this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->user = $this->user;
      $this->model->edit($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Anketa upravena.");
      $this->redirect("Polls:");
    };
    $form->setDefaults($poll->toArray());
    return $form;
  }
}
?>