<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Orm\Town as TownEntity,
    Nexendrie\Model\TownNotFoundException,
    Nexendrie\Forms\ManageTownFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\ItemNotFoundException,
    Nexendrie\Model\ItemNotOwnedException,
    Nexendrie\Model\ItemNotEquipableException,
    Nexendrie\Model\ItemAlreadyWornException,
    Nexendrie\Model\ItemNotWornException,
    Nexendrie\Model\ItemNotDrinkableException,
    Nexendrie\Model\HealingNotNeeded,
    Nexendrie\Forms\AppointMayorFormFactory,
    Nexendrie\Model\ItemNotForSaleException,
    Nexendrie\Model\ItemNotUpgradableException,
    Nexendrie\Model\ItemMaxLevelReachedException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Forms\MakeCitizenFormFactory;

/**
 * Presenter Property
 *
 * @author Jakub Konečný
 */
class PropertyPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Property @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Town @autowire */
  protected $townModel;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  /** @var \Nexendrie\Model\Inventory @autowire */
  protected $inventoryModel;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  /** @var \Nexendrie\Model\Combat @autowire */
  protected $combatModel;
  /** @var \Nexendrie\Model\UserManager @autowire */
  protected $userManager;
  /** @var TownEntity */
  private $town;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @return void
   */
  function renderDefault(): void {
    $data = $this->inventoryModel->possessions();
    $this->template->money = $data["money"];
    $this->template->items = $data["items"];
    $this->template->towns = $data["towns"];
    $this->template->loan = $data["loan"];
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionTown(int $id): void {
    try {
      $this->town = $this->townModel->get($id);
    } catch(TownNotFoundException $e) {
      $this->flashMessage("Město nenalezeno.");
      $this->redirect("Homepage:");
    }
    if($this->town->owner->id != $this->user->id) {
      $this->flashMessage("Zadané město ti nepatří.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderTown(int $id): void {
    $this->template->town = $this->town;
    $this->template->mayor = $this->townModel->getMayor($this->town->id);
  }
  
  /**
   * @param ManageTownFormFactory $factory
   * @return Form
   */
  protected function createComponentManageTownForm(ManageTownFormFactory $factory): Form {
    $form = $factory->create($this->town->id);
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  protected function createComponentAppointMayorForm(AppointMayorFormFactory $factory): Form {
    $form = $factory->create($this->town->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Rychář jmenován.");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function renderBudget(): void {
    $budget = $this->model->budget();
    $this->template->incomes = $this->localeModel->money(array_sum($budget["incomes"]));
    $this->template->expenses = $this->localeModel->money(array_sum($budget["expenses"]));
    $this->template->budget = $this->localeModel->money(array_sum($budget["incomes"]) - array_sum($budget["expenses"]));
    $this->template->work = $this->localeModel->money($budget["incomes"]["work"]);
    $this->template->adventures = $this->localeModel->money($budget["incomes"]["adventures"]);
    $this->template->beerProduction = $this->localeModel->money($budget["incomes"]["beerProduction"]);
    $this->template->taxes = $this->localeModel->money($budget["incomes"]["taxes"]);
    $this->template->incomeTax = $this->localeModel->money($budget["expenses"]["incomeTax"]);
    $this->template->loansInterest = $this->localeModel->money($budget["expenses"]["loansInterest"]);
    $this->template->membershipFee = $this->localeModel->money($budget["expenses"]["membershipFee"]);
  }
  
  /**
   * @return void
   */
  function renderEquipment(): void {
    $this->template->items = $this->inventoryModel->equipment();
    $this->template->currentSet = $this->inventoryModel->getUserItemSet($this->user->id);
  }
  
  /**
   * @return void
   */
  function renderPotions(): void {
    $user = $this->userManager->get($this->user->id);
    $this->template->life = $user->life;
    $this->template->maxLife = $user->maxLife;
    $this->template->items = $this->inventoryModel->potions();
    $combatLife = $this->combatModel->calculateUserLife($user);
    $this->template->lifeCombat = $combatLife["life"];
    $this->template->maxLifeCombat = $combatLife["maxLife"];
  }
  
  /**
   * @param int $item
   * @return void
   */
  function handleEquip(int $item): void {
    try {
      $this->inventoryModel->equipItem($item);
      $this->flashMessage("Věc nasazena.");
    } catch(ItemNotFoundException $e) {
      $this->flashMessage("Věc nenalezena.");
    } catch(ItemNotOwnedException $e) {
      $this->flashMessage("Zadaná věc ti nepatří.");
    } catch(ItemNotEquipableException $e) {
      $this->flashMessage("Zadanou věc nelze nasadit.");
    } catch(ItemAlreadyWornException $e) {
      $this->flashMessage("Už nosíš danou věc.");
    }
    $this->redirect("equipment");
  }
  
  /**
   * @param int $item
   * @return void
   */
  function handleUnequip(int $item): void {
    try {
      $this->inventoryModel->unequipItem($item);
      $this->flashMessage("Věc sundána.");
    } catch(ItemNotFoundException $e) {
      $this->flashMessage("Věc nenalezena.");
    } catch(ItemNotOwnedException $e) {
      $this->flashMessage("Zadaná věc ti nepatří.");
    } catch(ItemNotWornException $e) {
      $this->flashMessage("Nenosíš danou věc.");
    }
    $this->redirect("equipment");
  }
  
  /**
   * @param int $potion
   * @return void
   */
  function handleDrink(int $potion): void {
    try {
      $life = $this->inventoryModel->drinkPotion($potion);
      $this->flashMessage("Doplnil sis $life životů.");
    } catch(ItemNotFoundException $e) {
      $this->flashMessage("Věc nenalezena.");
    } catch(ItemNotOwnedException $e) {
      $this->flashMessage("Zadaná věc ti nepatří.");
    } catch(ItemNotDrinkableException $e) {
      $this->flashMessage("Zadanou věc nelze vypít.");
    } catch(HealingNotNeeded $e) {
      $this->flashMessage("Nepotřebuješ léčení.");
    }
    $this->redirect("equipment");
  }
  
  /**
   * @param int $item
   * @return void
   */
  function handleSell(int $item): void {
    try {
      $price = $this->inventoryModel->sellItem($item);
      $this->flashMessage("Věc prodána za " . $this->localeModel->money($price) . ".");
    } catch(ItemNotFoundException $e) {
      $this->flashMessage("Věc nenalezena.");
    } catch(ItemNotOwnedException $e) {
      $this->flashMessage("Zadaná věc ti nepatří.");
    } catch(ItemNotForSaleException $e) {
      $this->flashMessage("Zadanou věc nelze prodat.");
    }
    $this->redirect("equipment");
  }
  
  /**
   * @param int $item
   * @return void
   */
  function handleUpgrade(int $item): void {
    try {
      $this->inventoryModel->upgradeItem($item);
      $this->flashMessage("Věc vylepšena.");
    } catch(ItemNotFoundException $e) {
      $this->flashMessage("Věc nenalezena.");
    } catch(ItemNotOwnedException $e) {
      $this->flashMessage("Zadaná věc ti nepatří.");
    } catch(ItemNotUpgradableException $e) {
      $this->flashMessage("Zadanou věc nelze vylepšit.");
    } catch(ItemMaxLevelReachedException $e) {
      $this->flashMessage("Zadanou věc už nelze vylepšit.");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nemáš dostatek peněz.");
    }
    $this->redirect("equipment");
  }
  
  /**
   * @param MakeCitizenFormFactory $factory
   * @return Form
   */
  protected function createComponentMakeCitizenForm(MakeCitizenFormFactory $factory): Form {
    $form = $factory->create($this->town->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Provedeno.");
    };
    return $form;
  }
}
?>