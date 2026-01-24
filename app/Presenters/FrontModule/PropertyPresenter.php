<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CombatHelper;
use Nexendrie\Model\Inventory;
use Nexendrie\Model\Locale;
use Nexendrie\Model\Property;
use Nexendrie\Model\Town;
use Nexendrie\Model\UserManager;
use Nexendrie\Orm\Town as TownEntity;
use Nexendrie\Model\TownNotFoundException;
use Nexendrie\Forms\ManageTownFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\ItemNotFoundException;
use Nexendrie\Model\ItemNotOwnedException;
use Nexendrie\Model\ItemNotEquipableException;
use Nexendrie\Model\ItemAlreadyWornException;
use Nexendrie\Model\ItemNotWornException;
use Nexendrie\Model\ItemNotDrinkableException;
use Nexendrie\Model\HealingNotNeededException;
use Nexendrie\Forms\AppointMayorFormFactory;
use Nexendrie\Model\ItemNotForSaleException;
use Nexendrie\Model\ItemNotUpgradableException;
use Nexendrie\Model\ItemMaxLevelReachedException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Forms\MakeCitizenFormFactory;

/**
 * Presenter Property
 *
 * @author Jakub Konečný
 */
final class PropertyPresenter extends BasePresenter
{
    private TownEntity $town;

    public function __construct(
        private readonly Property $model,
        private readonly Town $townModel,
        private readonly Locale $localeModel,
        private readonly Inventory $inventoryModel,
        private readonly CombatHelper $combatModel,
        private readonly UserManager $userManager
    ) {
        parent::__construct();
        $this->cachingEnabled = false;
    }

    protected function startup(): void
    {
        parent::startup();
        $this->requiresLogin();
        $this->mustNotBeBanned();
    }

    public function renderDefault(): void
    {
        $data = $this->inventoryModel->possessions();
        $this->template->money = $data["money"];
        $this->template->items = $data["items"];
        $this->template->towns = $data["towns"];
        $this->template->loan = $data["loan"];
        if ($this->user->isAllowed("town", "manage")) {
            $this->template->royalTowns = $this->townModel->listOfTowns()->findBy(["owner" => 0]);
        }
        $this->template->path = $this->user->identity->path;
    }

    public function actionTown(int $id): void
    {
        try {
            $this->town = $this->townModel->get($id);
        } catch (TownNotFoundException) {
            $this->flashMessage("Město nenalezeno.");
            $this->redirect("Homepage:");
        }
        if (!$this->townModel->canManage($this->town)) {
            $this->flashMessage("Zadané město ti nepatří.");
            $this->redirect("Homepage:");
        }
    }

    public function renderTown(int $id): void
    {
        $this->template->town = $this->town;
        $this->template->mayor = $this->townModel->getMayor($this->town->id);
    }

    protected function createComponentManageTownForm(ManageTownFormFactory $factory): Form
    {
        $form = $factory->create($this->town->id);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Změny uloženy.");
        };
        return $form;
    }

    protected function createComponentAppointMayorForm(AppointMayorFormFactory $factory): Form
    {
        $form = $factory->create($this->town->id);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Rychtář jmenován.");
        };
        return $form;
    }

    public function renderBudget(): void
    {
        $budget = $this->model->budget();
        $this->template->incomes = (int) array_sum($budget["incomes"]);
        $this->template->expenses = (int) array_sum($budget["expenses"]);
        $this->template->budget = (int) array_sum($budget["incomes"]) - (int) array_sum($budget["expenses"]);
        $this->template->work = $budget["incomes"]["work"];
        $this->template->adventures = $budget["incomes"]["adventures"];
        $this->template->beerProduction = $budget["incomes"]["beerProduction"];
        $this->template->taxes = $budget["incomes"]["taxes"];
        $this->template->depositInterest = $budget["incomes"]["depositInterest"];
        $this->template->incomeTax = $budget["expenses"]["incomeTax"];
        $this->template->loansInterest = $budget["expenses"]["loansInterest"];
        $this->template->membershipFee = $budget["expenses"]["membershipFee"];
        $this->template->mountsMaintenance = $budget["expenses"]["mountsMaintenance"];
        $this->template->buildingMaintenance = $budget["expenses"]["buildingMaintenance"];
    }

    public function renderEquipment(): void
    {
        $this->template->items = $this->inventoryModel->equipment();
        $this->template->currentSet = $this->inventoryModel->getUserItemSet($this->user->id);
    }

    public function renderPotions(): void
    {
        $user = $this->userManager->get($this->user->id);
        $this->template->life = $user->life;
        $this->template->maxLife = $user->maxLife;
        $this->template->items = $this->inventoryModel->potions();
        $combatLife = $this->combatModel->calculateUserLife($user);
        $this->template->lifeCombat = $combatLife["life"];
        $this->template->maxLifeCombat = $combatLife["maxLife"];
    }

    public function handleEquip(int $item): never
    {
        try {
            $this->inventoryModel->equipItem($item);
            $this->flashMessage("Věc nasazena.");
        } catch (ItemNotFoundException) {
            $this->flashMessage("Věc nenalezena.");
        } catch (ItemNotOwnedException) {
            $this->flashMessage("Zadaná věc ti nepatří.");
        } catch (ItemNotEquipableException) {
            $this->flashMessage("Zadanou věc nelze nasadit.");
        } catch (ItemAlreadyWornException) {
            $this->flashMessage("Už nosíš danou věc.");
        }
        $this->redirect("equipment");
    }

    public function handleUnequip(int $item): void
    {
        try {
            $this->inventoryModel->unequipItem($item);
            $this->flashMessage("Věc sundána.");
        } catch (ItemNotFoundException) {
            $this->flashMessage("Věc nenalezena.");
        } catch (ItemNotOwnedException) {
            $this->flashMessage("Zadaná věc ti nepatří.");
        } catch (ItemNotWornException) {
            $this->flashMessage("Nenosíš danou věc.");
        }
        $this->redirect("equipment");
    }

    public function handleDrink(int $potion): never
    {
        try {
            $life = $this->inventoryModel->drinkPotion($potion);
            $this->flashMessage("Doplnil sis $life životů.");
        } catch (ItemNotFoundException) {
            $this->flashMessage("Věc nenalezena.");
        } catch (ItemNotOwnedException) {
            $this->flashMessage("Zadaná věc ti nepatří.");
        } catch (ItemNotDrinkableException) {
            $this->flashMessage("Zadanou věc nelze vypít.");
        } catch (HealingNotNeededException) {
            $this->flashMessage("Nepotřebuješ léčení.");
        }
        $this->redirect("equipment");
    }

    public function handleSell(int $item): never
    {
        try {
            $price = $this->inventoryModel->sellItem($item);
            $this->flashMessage("Věc prodána za " . $this->localeModel->money($price) . ".");
        } catch (ItemNotFoundException) {
            $this->flashMessage("Věc nenalezena.");
        } catch (ItemNotOwnedException) {
            $this->flashMessage("Zadaná věc ti nepatří.");
        } catch (ItemNotForSaleException) {
            $this->flashMessage("Zadanou věc nelze prodat.");
        }
        $this->redirect("equipment");
    }

    public function handleUpgrade(int $item): never
    {
        try {
            $this->inventoryModel->upgradeItem($item);
            $this->flashMessage("Věc vylepšena.");
        } catch (ItemNotFoundException) {
            $this->flashMessage("Věc nenalezena.");
        } catch (ItemNotOwnedException) {
            $this->flashMessage("Zadaná věc ti nepatří.");
        } catch (ItemNotUpgradableException) {
            $this->flashMessage("Zadanou věc nelze vylepšit.");
        } catch (ItemMaxLevelReachedException) {
            $this->flashMessage("Zadanou věc už nelze vylepšit.");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nemáš dostatek peněz.");
        }
        $this->redirect("equipment");
    }

    protected function createComponentMakeCitizenForm(MakeCitizenFormFactory $factory): Form
    {
        $form = $factory->create($this->town->id);
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Provedeno.");
        };
        return $form;
    }
}
