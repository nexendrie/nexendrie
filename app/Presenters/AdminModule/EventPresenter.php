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
  
  protected function startup(): void {
    parent::startup();
    $this->requiresPermissions("content", "list");
  }
  
  public function renderDefault(): void {
    $this->template->events = $this->model->listOfEvents();
  }
  
  public function actionAdd(): void {
    $this->requiresPermissions("event", "add");
  }
  
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
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("event", "edit");
    try {
      $this->event = $this->model->getEvent($id);
    } catch(EventNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
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
   * @throws \Nette\Application\BadRequestException
   */
  public function actionDelete(int $id): void {
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