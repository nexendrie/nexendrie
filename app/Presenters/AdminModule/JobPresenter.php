<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditJobFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\Job;
use Nexendrie\Orm\Job as JobEntity;
use Nexendrie\Model\JobNotFoundException;

/**
 * Presenter Job
 *
 * @author Jakub Konečný
 */
final class JobPresenter extends BasePresenter
{
    private JobEntity $job;

    public function __construct(private readonly Job $model)
    {
        parent::__construct();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id): void
    {
        $this->requiresPermissions("content", "edit");
        try {
            $this->job = $this->model->getJob($id);
        } catch (JobNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    public function actionNew(): void
    {
        $this->requiresPermissions("content", "add");
    }

    protected function createComponentAddJobForm(AddEditJobFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Práce přidána.");
            $this->redirect("Content:jobs");
        };
        return $form;
    }

    protected function createComponentEditJobForm(AddEditJobFormFactory $factory): Form
    {
        $form = $factory->create($this->job);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Změny uloženy.");
            $this->redirect("Content:jobs");
        };
        return $form;
    }
}
