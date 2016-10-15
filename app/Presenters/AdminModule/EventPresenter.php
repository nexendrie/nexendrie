<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditEventFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\EventNotFoundException,
    Nexendrie\Model\CannotDeleteStartedEventException;

/**
 * Presenter Event
 *
 * @author Jakub Konečný
 */
class EventPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Events @autowire */
  protected $model;
  /** @var \Nexendrie\Orm\Event */
  private $event;
  
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
    $this->template->events = $this->model->listOfEvents();
  }
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("event", "add");
  }
  
  /**
   * @param AddEditEventFormFactory $factory
   * @return Form
   */
  protected function createComponentAddEventForm(AddEditEventFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSubmit[] = function(Form $form, array $values) {
      $this->model->addEvent($values);
      $this->flashMessage("Akce přidána.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id) {
    $this->requiresPermissions("event", "edit");
    try {
      $this->event = $this->model->getEvent($id);
    } catch(EventNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditEventFormFactory $factory
   * @return Form
   */
  protected function createComponentEditEventForm(AddEditEventFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->event->dummyArray());
    $form->onSubmit[] = function(Form $form, array $values) {
      $this->model->editEvent($this->getParameter("id"), $values);
      $this->flashMessage("Akce upravena.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionDelete(int $id) {
    $this->requiresPermissions("event", "delete");
    try {
      $this->model->deleteEvent($id);
      $this->flashMessage("Akce smazána.");
      $this->redirect("default");
    } catch(EventNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    } catch(CannotDeleteStartedEventException $e) {
      $this->flashMessage("Nelze smazat již započatnou akci.");
      $this->redirect("Homepage:");
    }
  }
}
?>