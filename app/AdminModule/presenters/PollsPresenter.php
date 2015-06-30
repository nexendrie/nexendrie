<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI;

/**
 * Presenter Polls
 *
 * @author Jakub Konečný
 */
class PollsPresenter extends BasePresenter {
  /** @var \Nexendrie\Polls */
  protected $model;
  
  /**
   * @param \Nexendrie\Polls $model
   */
  function __construct(\Nexendrie\Polls $model) {
    $this->model = $model;
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
   * Creates form for adding poll
   * 
   * @return \Nette\Application\UI\Form
   */
  function createComponentAddPollForm() {
    $factory = new \Nexendrie\Forms\AddEditPollForm;
    $form = $factory->create();
    $form->onSuccess[] = array($this, "addPollFormSucceeded");
    return $form;
  }
  
  /**
   * Add poll
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function addPollFormSucceeded(UI\Form $form, $values) {
    $this->model->user = $this->context->getService("security.user");
    $this->model->add($values);
    $this->flashMessage("Anketa přidána.");
    $this->redirect("Polls:");
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("poll", "add");
    if(!$this->model->exists($id)) $this->forward("notfound");
  }
  
  /**
   * Creates form for editing poll
   * 
   * @return \Nette\Application\UI\Form
   */
  function createComponentEditPollForm() {
    $poll = $this->model->view($this->getParameter("id"));
    $factory = new \Nexendrie\Forms\AddEditPollForm;
    $form = $factory->create();
    $form->onSuccess[] = array($this, "editPollFormSucceeded");
    $form->setDefaults((array) $poll);
    return $form;
  }
  
  /**
   * Add poll
   * 
   * @param \Nette\Application\UI\Form $form
   * @param \Nette\Utils\ArrayHash $values
   */
  function editPollFormSucceeded(UI\Form $form, $values) {
    $this->model->user = $this->context->getService("security.user");
    $this->model->edit($this->getParameter("id"), $values);
    $this->flashMessage("Anketa upravena.");
  }
}
?>