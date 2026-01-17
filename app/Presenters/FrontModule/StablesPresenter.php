<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Mount;
use Nexendrie\Orm\Mount as MountEntity;
use Nexendrie\Model\MountNotFoundException;
use Nexendrie\Forms\ManageMountFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Components\StablesControlFactory;
use Nexendrie\Components\StablesControl;

/**
 * Presenter Stables
 *
 * @author Jakub Konečný
 */
final class StablesPresenter extends BasePresenter
{
    private MountEntity $mount;
    protected bool $publicCache = false;

    public function __construct(private readonly Mount $model)
    {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();
        $this->requiresLogin();
        $this->mustNotBeBanned();
        $this->mustNotBeTavelling();
    }

    public function renderDefault(): void
    {
        $this->template->mounts = $this->model->listOfMounts($this->user->id);
    }

    protected function createComponentStables(StablesControlFactory $factory): StablesControl
    {
        return $factory->create();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionManage(int $id): void
    {
        try {
            $this->mount = $this->model->get($id);
        } catch (MountNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
        if ($this->mount->owner->id !== $this->user->id) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function createComponentManageMountForm(ManageMountFormFactory $factory): Form
    {
        $form = $factory->create($this->mount->id);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Změny uloženy.");
        };
        return $form;
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionTrain(int $id): void
    {
        try {
            $mount = $this->model->get($id);
        } catch (MountNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
        if ($mount->owner->id !== $this->user->id) {
            throw new \Nette\Application\BadRequestException();
        }
        $this->template->mountId = $id;
    }

    protected function getDataModifiedTime(): int
    {
        if (isset($this->template->mounts)) {
            $time = 0;
            /** @var \Nexendrie\Orm\Mount $mount */
            foreach ($this->template->mounts as $mount) {
                $time = max($time, $mount->updated);
            }
            return $time;
        }
        if (isset($this->mount)) {
            return time();
        }
        return 0;
    }
}
