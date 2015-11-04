<?php
namespace Nexendrie\FrontModule\Presenters;

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
    Nexendrie\Model\HealingNotNeeded;

/**
 * Presenter Assets
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
  /** @var \Nexendrie\Model\Equipment @autowire */
  protected $equipmentModel;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  /** @var TownEntity */
  private $town;
  
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $data = $this->model->show();
    $this->template->money = $data["money"];
    $this->template->items = $data["items"];
    $this->template->isLord = $data["isLord"];
    $this->template->towns = $data["towns"];
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionTown($id) {
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
  function renderTown($id) {
    $this->template->town = $this->town;
  }
  
  /**
   * @param ManageTownFormFactory $factory
   * @return Form
   */
  protected function createComponentManageTownForm(ManageTownFormFactory $factory) {
    $form = $factory->create($this->town->id);
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function renderBudget() {
    $budget = $this->model->budget();
    $this->template->incomes = $this->localeModel->money(array_sum($budget["incomes"]));
    $this->template->expenses = $this->localeModel->money(array_sum($budget["expenses"]));
    $this->template->budget = $this->localeModel->money(array_sum($budget["incomes"]) - array_sum($budget["expenses"]));
    $this->template->work = $this->localeModel->money($budget["incomes"]["work"]);
    $this->template->adventures = $this->localeModel->money($budget["incomes"]["adventures"]);
    $this->template->taxes = $this->localeModel->money($budget["incomes"]["taxes"]);
    $this->template->incomeTax = $this->localeModel->money($budget["expenses"]["incomeTax"]);
    $this->template->loansInterest = $this->localeModel->money($budget["expenses"]["loansInterest"]);
  }
  
  /**
   * @return void
   */
  function renderEquipment() {
    $this->template->items = $this->model->equipment();
  }
  
  /**
   * @return void
   */
  function renderPotions() {
    $life = $this->profileModel->userLife();
    $this->template->life = $life[0];
    $this->template->maxLife = $life[1];
    $this->template->items = $this->model->potions();
  }
  
  /**
   * @param int $item
   * @return void
   */
  function handleEquip($item) {
    try {
      $this->equipmentModel->equipItem($item);
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
  function handleUnequip($item) {
    try {
      $this->equipmentModel->unequipItem($item);
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
   function handleDrink($potion) {
     try {
       $life = $this->model->drinkPotion($potion);
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
   }
}
?>