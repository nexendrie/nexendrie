<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Chat\ITownChatControlFactory;
use Nexendrie\Chat\TownChatControl;
use Nexendrie\Model\Locale;
use Nexendrie\Model\Profile;
use Nexendrie\Model\Town;
use Nexendrie\Model\TownNotFoundException;
use Nexendrie\Model\CannotMoveToSameTownException;
use Nexendrie\Model\CannotMoveToTownException;
use Nexendrie\Forms\FoundTownFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Components\IElectionsControlFactory;
use Nexendrie\Components\ElectionsControl;
use Nexendrie\Model\UserManager;
use Nexendrie\Orm\Group as GroupEntity;
use Nexendrie\Orm\Model as ORM;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
final class TownPresenter extends BasePresenter
{
    private \Nexendrie\Orm\Town $town;
    protected bool $cachingEnabled = false;

    public function __construct(private readonly Town $model, private readonly UserManager $userManager, private readonly Profile $profileModel, private readonly Locale $localeModel, private readonly ORM $orm, private readonly ITownChatControlFactory $chatFactory)
    {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();
        if ($this->action !== "detail" && $this->action !== "list") {
            $this->requiresLogin();
        }
    }

    protected function getChat(): TownChatControl
    {
        return $this->chatFactory->create();
    }

    public function renderDefault(): void
    {
        $user = $this->userManager->get($this->user->id);
        $this->template->town = $user->town;
        $this->template->path = $user->group->path;
        $this->template->house = $user->house;
        $this->template->guild = $user->guild;
        $this->template->order = $user->order;
        $this->publicCache = false;
    }

    public function renderList(): void
    {
        $this->template->towns = $this->model->listOfTowns();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function renderDetail(int $id): void
    {
        try {
            $this->template->town = $this->model->get($id);
            if (!$this->user->isLoggedIn()) {
                $this->template->canMove = false;
            } elseif ($id === $this->user->identity->town) {
                $this->template->canMove = false;
            } else {
                $this->template->canMove = $this->model->canMove();
            }
            $this->template->canManage = $this->model->canManage($this->template->town);
        } catch (TownNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    public function actionMove(int $id): never
    {
        try {
            $this->model->moveToTown($id);
            /** @var \Nexendrie\Model\Authenticator $authenticator */
            $authenticator = $this->user->authenticator;
            $authenticator->user = $this->user;
            $authenticator->refreshIdentity();
            $message = $this->localeModel->genderMessage("Přestěhoval(a) jsi se do vybraného města.");
            $this->flashMessage($message);
            $this->redirect("Town:");
        } catch (TownNotFoundException) {
            $this->flashMessage("Město nebylo nalezeno.");
            $this->redirect("Homepage:");
        } catch (CannotMoveToSameTownException) {
            $this->flashMessage("V tomto městě již žiješ.");
            $this->redirect("Homepage:");
        } catch (CannotMoveToTownException) {
            $this->flashMessage("Nemůžeš se přesunout do jiného města.");
            $this->redirect("Homepage:");
        }
    }

    public function actionFound(): void
    {
        $path = $this->profileModel->getPath();
        if ($path !== GroupEntity::PATH_TOWER) {
            $this->flashMessage("Jen šlechtici mohou zakládat města.");
            $this->redirect("Homepage:");
        }
    }

    public function renderFound(): void
    {
        $this->template->foundTownFee = $this->sr->settings['fees']['foundTown'];
        $this->template->foundingCharter = $this->orm->items->getById($this->sr->settings["specialItems"]["foundTown"]);
    }

    protected function createComponentFoundTownForm(FoundTownFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Město založeno.");
            $this->redirect("Homepage:");
        };
        return $form;
    }

    public function actionElections(): void
    {
        $this->requiresPermissions("town", "elect");
        $this->town = $this->model->get($this->user->identity->town);
    }

    protected function createComponentElections(IElectionsControlFactory $factory): ElectionsControl
    {
        $elections = $factory->create();
        $elections->town = $this->town;
        return $elections;
    }
}
