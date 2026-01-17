<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\AlreadyOnAdventureException;
use Nexendrie\Model\AdventureNotFoundException;
use Nexendrie\Model\InsufficientLevelForAdventureException;
use Nexendrie\Model\MountNotFoundException;
use Nexendrie\Model\MountNotOwnedException;
use Nexendrie\Model\MountInBadConditionException;
use Nexendrie\Model\NotOnAdventureException;
use Nexendrie\Model\NoEnemyRemainException;
use Nexendrie\Model\NotAllEnemiesDefeatedException;
use Nexendrie\Model\CannotDoAdventureException;
use Nexendrie\Model\AdventureNotAccessibleException;
use Nexendrie\Orm\UserAdventure;

/**
 * AdventureControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class AdventureControl extends \Nette\Application\UI\Control
{
    public function __construct(
        private readonly \Nexendrie\Model\Adventure $model,
        private readonly \Nexendrie\Model\Locale $localeModel,
        private readonly \Nette\Security\User $user
    ) {
    }

    public function renderList(): void
    {
        $this->template->setFile(__DIR__ . "/adventureList.latte");
        $this->template->adventures = $this->model->findAvailableAdventures();
        $this->template->render();
    }

    public function renderMounts(int $adventure): void
    {
        $this->template->setFile(__DIR__ . "/adventureMounts.latte");
        $this->template->mounts = $this->model->findGoodMounts();
        $this->template->adventure = $adventure;
        $this->template->render();
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . "/adventure.latte");
        /** @var UserAdventure $adventure */
        $adventure = $this->model->getCurrentAdventure();
        $this->template->adventure = $adventure;
        $this->template->nextEnemy = $adventure->nextEnemy;
        $this->template->render();
    }

    public function handleStart(int $adventure, int $mount): void
    {
        try {
            $this->model->startAdventure($adventure, $mount);
            /** @var \Nexendrie\Model\Authenticator $authenticator */
            $authenticator = $this->user->authenticator;
            $authenticator->user = $this->user;
            $authenticator->refreshIdentity();
            $message = $this->localeModel->genderMessage("Vydal(a) jsi se na dobrodružství.");
            $this->presenter->flashMessage($message);
            $this->presenter->redirect("Adventure:");
        } catch (AlreadyOnAdventureException) {
            $this->presenter->flashMessage("Už jsi na dobrodružství.");
        } catch (CannotDoAdventureException) {
            $this->presenter->flashMessage("Musíš počkat před dalším dobrodružstvím.");
        } catch (AdventureNotFoundException) {
            $this->presenter->flashMessage("Dobrodružství nenalezeno.");
        } catch (InsufficientLevelForAdventureException) {
            $this->presenter->flashMessage("Nemáš dostatečnou úroveň pro toto dobrodružství.");
        } catch (MountNotFoundException) {
            $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
        } catch (MountNotOwnedException) {
            $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
        } catch (MountInBadConditionException) {
            $this->presenter->flashMessage("Dané jezdecké zvíře je ve špatném stavu.");
        } catch (AdventureNotAccessibleException) {
            $this->presenter->flashMessage("Vybrané dobrodružství není dostupné.");
        }
    }

    public function handleFight(): void
    {
        try {
            $result = $this->model->fight();
            $this->template->message = $result["message"];
        } catch (NotOnAdventureException) {
            $this->presenter->flashMessage("Nejsi na dobrodružství.");
        } catch (NoEnemyRemainException) {
            $message = $this->localeModel->genderMessage("Porazil(a) jsi již všechny nepřátele.");
            $this->presenter->flashMessage($message);
        }
    }

    public function handleFinish(): void
    {
        try {
            $this->model->finishAdventure();
            /** @var \Nexendrie\Model\Authenticator $authenticator */
            $authenticator = $this->user->authenticator;
            $authenticator->user = $this->user;
            $authenticator->refreshIdentity();
            $this->presenter->redirect("Homepage:");
        } catch (NotOnAdventureException) {
            $this->presenter->flashMessage("Nejsi na dobrodružství.");
        } catch (NotAllEnemiesDefeatedException) {
            $message = $this->localeModel->genderMessage("Neporazil(a) jsi již všechny nepřátele.");
            $this->presenter->flashMessage($message);
        }
    }
}
