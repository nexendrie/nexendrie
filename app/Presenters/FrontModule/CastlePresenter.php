<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Castle;
use Nexendrie\Model\CastleNotFoundException;
use Nexendrie\Forms\BuildCastleFormFactory;
use Nexendrie\Forms\ManageCastleFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\CannotUpgradeCastleException;
use Nexendrie\Model\CannotRepairCastleException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\Profile;
use Nexendrie\Model\UserManager;
use Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter Castle
 *
 * @author Jakub Konečný
 */
final class CastlePresenter extends BasePresenter
{
    public function __construct(
        private readonly Castle $model,
        private readonly UserManager $userManager,
        private readonly Profile $profileModel
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();
        if ($this->action !== "detail" && $this->action !== "list") {
            $this->requiresLogin();
        }
    }

    public function actionDefault(): void
    {
        $castle = $this->model->getUserCastle();
        if ($castle === null) {
            $this->flashMessage("Nemáš hrad.");
            if ($this->profileModel->getPath() === GroupEntity::PATH_TOWER) {
                $this->redirect("build");
            }
            $this->redirect("Homepage:");
        }
        $this->template->castle = $castle;
        $this->template->canUpgrade = $this->model->canUpgrade();
    }

    public function renderList(): void
    {
        $this->template->castles = $this->model->listOfCastles();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function renderDetail(int $id): void
    {
        try {
            $this->template->castle = $this->model->getCastle($id);
        } catch (CastleNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    public function actionBuild(): void
    {
        $user = $this->userManager->get($this->user->id);
        if ($user->group->path !== GroupEntity::PATH_TOWER) {
            $this->flashMessage("Nejsi šlechtic.");
            $this->redirect("Homepage:");
        } elseif ($this->model->getUserCastle() !== null) {
            $this->flashMessage("Můžeš postavit jen 1 hrad.");
            $this->redirect("default");
        }
        $this->template->buildingPrice = $this->sr->settings["fees"]["buildCastle"];
    }

    protected function createComponentBuildCastleForm(BuildCastleFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Hrad postaven.");
            $this->redirect("default");
        };
        return $form;
    }

    public function handleUpgrade(): never
    {
        try {
            $this->model->upgrade();
            $this->flashMessage("Hrad vylepšen.");
            $this->redirect("default");
        } catch (CannotUpgradeCastleException) {
            $this->flashMessage("Nemůžeš vylepšit hrad.");
            $this->redirect("Homepage:");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nedostatek peněz.");
            $this->redirect("manage");
        }
    }

    public function handleRepair(): never
    {
        try {
            $this->model->repair();
            $this->flashMessage("Hrad opraven.");
            $this->redirect("default");
        } catch (CannotRepairCastleException) {
            $this->flashMessage("Nemůžeš opravit hrad.");
            $this->redirect("Homepage:");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nedostatek peněz.");
            $this->redirect("manage");
        }
    }

    protected function createComponentManageCastleForm(ManageCastleFormFactory $factory): Form
    {
        $form = $factory->create($this->template->castle->id);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Změny uloženy.");
        };
        return $form;
    }

    protected function getDataModifiedTime(): int
    {
        if (isset($this->template->castle)) {
            return ($this->template->castle->updated);
        }
        if (isset($this->template->castles)) {
            $time = 0;
            /** @var \Nexendrie\Orm\Castle $castle */
            foreach ($this->template->castles as $castle) {
                $time = max($time, $castle->updated);
            }
            return $time;
        }
        return 0;
    }
}
