<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\AddEditPollFormFactory;
use Nexendrie\Model\Polls;

/**
 * Presenter Polls
 *
 * @author Jakub Konečný
 */
final class PollsPresenter extends BasePresenter
{
    public function __construct(private readonly Polls $model)
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
        $this->template->polls = $this->model->all();
    }

    public function actionAdd(): void
    {
        $this->requiresPermissions("poll", "add");
    }

    protected function createComponentAddPollForm(AddEditPollFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Anketa přidána.");
            $this->redirect("Polls:");
        };
        return $form;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id): void
    {
        $this->requiresPermissions("poll", "add");
        if (!$this->model->exists($id)) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function createComponentEditPollForm(AddEditPollFormFactory $factory): Form
    {
        $poll = $this->model->view((int) $this->getParameter("id"));
        $form = $factory->create($poll);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Anketa upravena.");
            $this->redirect("Polls:");
        };
        return $form;
    }
}
