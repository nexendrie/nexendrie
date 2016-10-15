<?php
declare(strict_types=1);

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
  protected function startup() {
    parent::startup();
    $this->requiresPermissions("content", "list");
  }
  
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
   * @param AddEditPollFormFactory $factory
   * @return Form
   */
  protected function createComponentAddPollForm(AddEditPollFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->user = $this->user;
      $this->model->add($values);
      $this->flashMessage("Anketa přidána.");
      $this->redirect("Polls:");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id) {
    $this->requiresPermissions("poll", "add");
    if(!$this->model->exists($id)) throw new \Nette\Application\BadRequestException;
  }
  
  /**
   * @param AddEditPollFormFactory $factory
   * @return Form
   */
  protected function createComponentEditPollForm(AddEditPollFormFactory $factory): Form {
    $poll = $this->model->view($this->getParameter("id"));
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->user = $this->user;
      $this->model->edit($this->getParameter("id"), $values);
      $this->flashMessage("Anketa upravena.");
      $this->redirect("Polls:");
    };
    $form->setDefaults($poll->toArray());
    return $form;
  }
}
?>