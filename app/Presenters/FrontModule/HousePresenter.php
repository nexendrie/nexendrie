<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CannotBuyMoreHousesException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\CannotUpgradeHouseException,
    Nexendrie\Model\CannotRepairHouseException,
    Nexendrie\Model\CannotUpgradeBreweryException,
    Nexendrie\Model\CannotProduceBeerException,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter House
 *
 * @author Jakub Konečný
 */
class HousePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\House */
  protected $model;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function __construct(\Nexendrie\Model\House $model, \Nexendrie\Model\Profile $profileModel, \Nexendrie\Model\Locale $localeModel) {
    parent::__construct();
    $this->model = $model;
    $this->profileModel = $profileModel;
    $this->localeModel = $localeModel;
  }
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeTavelling();
    if($this->profileModel->getPath() != GroupEntity::PATH_CITY) {
      $this->redirect("Homepage:");
    }
  }
  
  public function renderDefault(): void {
    $house = $this->model->getUserHouse();
    if(is_null($house)) {
      $this->flashMessage("Nevlastníš dům.");
      $this->redirect("Homepage:");
    }
    $this->template->house = $house;
    $this->template->canUpgrade = $this->model->canUpgrade();
    $this->template->canUpgradeBrewery = $this->model->canUpgradeBrewery();
    $this->template->canProduceBeer = $this->model->canProduceBeer();
  }
  
  public function actionBuy(): void {
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
  
  public function handleUpgrade(): void {
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
  
  public function handleRepair(): void {
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
  
  public function handleUpgradeBrewery(): void {
    try {
      $newLevel = $this->model->upgradeBrewery();
      $message = ($newLevel === 1) ? "Pivovar pořízen." : "Pivovar vylepšen.";
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotUpgradeBreweryException $e) {
      $this->flashMessage("Nemůžeš vylepšit pivovar.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("default");
    }
  }
  
  public function handleProduceBeer(): void {
    try {
      $result = $this->model->produceBeer();
      $message = $this->localeModel->genderMessage("Uvařil(a) jsi ");
      $message .= $this->localeModel->barrels($result["amount"]);
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