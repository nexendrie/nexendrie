<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CannotBuyMoreHousesException;
use Nexendrie\Model\House;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\CannotUpgradeHouseException;
use Nexendrie\Model\CannotRepairHouseException;
use Nexendrie\Model\CannotUpgradeBreweryException;
use Nexendrie\Model\CannotProduceBeerException;
use Nexendrie\Model\Locale;
use Nexendrie\Model\Profile;
use Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter House
 *
 * @author Jakub Konečný
 */
final class HousePresenter extends BasePresenter
{
    protected bool $publicCache = false;

    public function __construct(
        private readonly House $model,
        private readonly Profile $profileModel,
        private readonly Locale $localeModel
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();
        $this->requiresLogin();
        $this->mustNotBeTravelling();
        if ($this->profileModel->getPath() !== GroupEntity::PATH_CITY) {
            $this->redirect("Homepage:");
        }
    }

    public function renderDefault(): void
    {
        $house = $this->model->getUserHouse();
        if ($house === null) {
            $this->flashMessage("Nevlastníš dům.");
            $this->redirect("Homepage:");
        }
        $this->template->house = $house;
        $this->template->canUpgrade = $this->model->canUpgrade();
        $this->template->canUpgradeBrewery = $this->model->canUpgradeBrewery();
        $this->template->canProduceBeer = $this->model->canProduceBeer();
    }

    public function actionBuy(): never
    {
        try {
            $this->model->buyHouse();
            $this->flashMessage("Dům zakoupen.");
            $this->redirect("default");
        } catch (CannotBuyMoreHousesException) {
            $this->flashMessage("Už vlastníš dům.");
            $this->redirect("default");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nemáš dostatek peněz.");
            $this->redirect("Homepage:");
        }
    }

    public function handleUpgrade(): never
    {
        try {
            $this->model->upgrade();
            $this->flashMessage("Dům vylepšen.");
            $this->redirect("default");
        } catch (CannotUpgradeHouseException) {
            $this->flashMessage("Nemůžeš vylepšit dům.");
            $this->redirect("Homepage:");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nedostatek peněz.");
            $this->redirect("default");
        }
    }

    public function handleRepair(): never
    {
        try {
            $this->model->repair();
            $this->flashMessage("Dům opraven.");
            $this->redirect("default");
        } catch (CannotRepairHouseException) {
            $this->flashMessage("Nemůžeš opravit dům.");
            $this->redirect("Homepage:");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nedostatek peněz.");
            $this->redirect("default");
        }
    }

    public function handleUpgradeBrewery(): never
    {
        try {
            $newLevel = $this->model->upgradeBrewery();
            $message = ($newLevel === 1) ? "Pivovar pořízen." : "Pivovar vylepšen.";
            $this->flashMessage($message);
            $this->redirect("default");
        } catch (CannotUpgradeBreweryException) {
            $this->flashMessage("Nemůžeš vylepšit pivovar.");
            $this->redirect("Homepage:");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nedostatek peněz.");
            $this->redirect("default");
        }
    }

    public function handleProduceBeer(): never
    {
        try {
            $result = $this->model->produceBeer();
            $message = $this->localeModel->genderMessage("Uvařil(a) jsi ");
            $message .= $this->localeModel->barrels($result["amount"]);
            $message .= " piva za ";
            $message .= $this->localeModel->money($result["amount"] * $result["price"]) . ".";
            $this->flashMessage($message);
            $this->redirect("default");
        } catch (CannotProduceBeerException) {
            $this->flashMessage("Nemůžeš vařit pivo.");
            $this->redirect("Homepage:");
        }
    }

    protected function getDataModifiedTime(): int
    {
        if (isset($this->template->house)) {
            return ($this->template->house->updated);
        }
        return 0;
    }
}
