<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CannotBuyMoreHousesException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\CannotUpgradeHouseException,
    Nexendrie\Model\CannotRepairHouseException,
    Nexendrie\Model\CannotUpgradeBreweryException,
    Nexendrie\Model\CannotProduceBeerException,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter House
 *
 * @author Jakub Konečný
 */
class HousePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\House @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeTavelling();
    if($this->profileModel->getPath() != GroupEntity::PATH_CITY) {
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $house = $this->model->getUserHouse();
    if(!$house) {
      $this->flashMessage("Nevlastníš dům.");
      $this->redirect("Homepage:");
    }
    $this->template->house = $house;
    $this->template->canUpgrade = $this->model->canUpgrade();
    $this->template->canUpgradeBrewery = $this->model->canUpgradeBrewery();
    $this->template->canProduceBeer = $this->model->canProduceBeer();
  }
  
  /**
   * @return void
   */
  function actionBuy() {
    try {
      $this->model->buyHouse();
      $this->flashMessage("Dům zakoupen.");
      $this->redirect("default");
    } catch(CannotBuyMoreHousesException $e) {
      $this->flashMessage("Už vlastníš dům.");
      $this->redirect("default");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nemáš dostatek peněz.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function handleUpgrade() {
    try {
      $this->model->upgrade();
      $this->flashMessage("Dům vylepšen.");
      $this->redirect("default");
    } catch(CannotUpgradeHouseException $e) {
      $this->flashMessage("Nemůžeš vylepšit dům.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function handleRepair() {
    try {
      $this->model->repair();
      $this->flashMessage("Dům opraven.");
      $this->redirect("default");
    } catch(CannotRepairHouseException $e) {
      $this->flashMessage("Nemůžeš opravit dům.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function handleUpgradeBrewery() {
    try {
      $newLevel = $this->model->upgradeBrewery();
      if($newLevel === 1) {
        $this->flashMessage("Pivovar pořízen.");
      } else {
        $this->flashMessage("Pivovar vylepšen.");
      }
      $this->redirect("default");
    } catch(CannotUpgradeBreweryException $e) {
      $this->flashMessage("Nemůžeš vylepšit pivovar.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function handleProduceBeer() {
    try {
      $result = $this->model->produceBeer();
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Uvařila jsi ";
      } else {
        $message = "Uvařil jsi ";
      }
      $message .= $result["amount"] . " ";
      $message .= $this->localeModel->plural("sud", "sudy", "sudů", $result["amount"]);
      $message .= " piva za ";
      $message .= $this->localeModel->money($result["amount"] * $result["price"]) . ".";
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotProduceBeerException $e) {
      $this->flashMessage("Nemůžeš vařit pivo.");
      $this->redirect("Homepage:");
    }
  }
}
?>