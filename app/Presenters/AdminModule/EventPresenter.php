<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditEventFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\EventNotFoundException;
use Nexendrie\Model\CannotDeleteStartedEventException;
use Nexendrie\Model\Events;

/**
 * Presenter Event
 *
 * @author Jakub Konečný
 */
final class EventPresenter extends BasePresenter
{
    private \Nexendrie\Orm\Event $event;

    public function __construct(private readonly Events $model)
    {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();
        $this->requiresPermissions("content", "list");
    }

    public function renderDefault(): void
    {
        $this->template->events = $this->model->listOfEvents();
    }

    public function actionAdd(): void
    {
        $this->requiresPermissions("event", "add");
    }

    protected function createComponentAddEventForm(AddEditEventFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Akce přidána.");
            $this->redirect("default");
        };
        return $form;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id): void
    {
        $this->requiresPermissions("event", "edit");
        try {
            $this->event = $this->model->getEvent($id);
        } catch (EventNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function createComponentEditEventForm(AddEditEventFormFactory $factory): Form
    {
        $form = $factory->create($this->event);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Akce upravena.");
            $this->redirect("default");
        };
        return $form;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionDelete(int $id): void
    {
        $this->requiresPermissions("event", "delete");
        try {
            $this->model->deleteEvent($id);
            $this->flashMessage("Akce smazána.");
            $this->redirect("default");
        } catch (EventNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        } catch (CannotDeleteStartedEventException) {
            $this->flashMessage("Nelze smazat již započatnou akci.");
            $this->redirect("Homepage:");
        }
    }
}
