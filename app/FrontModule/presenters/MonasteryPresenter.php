<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\MonasteryNotFoundException,
    Nexendrie\Model\NotInMonasteryException,
    Nexendrie\Model\CannotJoinMonasteryException,
    Nexendrie\Model\CannotPrayException,
    Nexendrie\Model\CannotLeaveMonasteryException,
    Nexendrie\Forms\BuildMonasteryFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Forms\MonasteryDonateFormFactory,
    Nexendrie\Forms\ManageMonasteryFormFactory,
    Nexendrie\Model\CannotJoinOwnMonasteryException,
    Nexendrie\Model\CannotUpgradeMonasteryException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\CannotRepairMonasteryException,
    Nexendrie\Orm\User as UserEntity;

/**
 * Presenter Monastery
 *
 * @author Jakub Konečný
 */
class MonasteryPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Monastery @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  /** @var int*/
  private $monasteryId;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    try {
      $this->template->monastery = $this->model->getByUser();
      $this->template->canPray = $this->model->canPray();
      $this->template->canLeave = $this->model->canLeave();
      $this->template->canBuild = $this->model->canBuild();
      $this->template->canManage = $this->model->canManage();
      $this->template->prayerLife = $this->model->prayerLife();
    } catch(NotInMonasteryException $e) {
      $this->flashMessage("Nejsi v klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->monasteries = $this->model->listOfMonasteries();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->monastery = $this->model->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @return void
   */
  function actionBuild() {
    if(!$this->model->canBuild()) {
      $this->flashMessage("Nemůžeš postavit klášter.");
      $this->redirect("Homepage:");
    }
    $this->template->buildingPrice = $this->localeModel->money($this->model->buildingPrice);
  }
  
  /**
   * @param BuildMonasteryFormFactory $factory
   * @return Form
   */
  protected function createComponentBuildMonasteryForm(BuildMonasteryFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[]= function(Form $form) {
      $this->flashMessage("Klášter založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionJoin($id) {
    try {
      $this->model->join($id);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Vstoupila jsi do kláštera";
      else $message = "Vstoupil jsi do kláštera";
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinMonasteryException $e) {
      $this->flashMessage("Nemůžeš vstoupit do kláštera.");
      $this->redirect("Homepage:");
    } catch(MonasteryNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    } catch(CannotJoinOwnMonasteryException $e) {
      $this->flashMessage("Už jsi v tomto klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function actionLeave() {
    try {
      $this->model->leave();
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Vystoupila jsi z kláštera.";
      else $message = "Vystoupil jsi z kláštera.";
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveMonasteryException $e) {
      $this->flashMessage("Nemůžeš vystoupit z kláštera.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function actionPray() {
    try {
      $this->model->pray();
      $this->flashMessage("Modlidba ti přidala 5 životů.");
      $this->redirect("default");
    } catch(CannotPrayException $e) {
      $this->flashMessage("Nemůžeš se modlit (právě teď).");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param MonasteryDonateFormFactory $factory
   * @return Form
   */
  protected function createComponentMonasteryDonateForm(MonasteryDonateFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Příspěvek proveden.");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function actionManage() {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat klášter.");
      $this->redirect("Homepage:");
    } else {
      $this->template->monastery = $monastery = $this->model->getByUser();
      $this->monasteryId = $monastery->id;
      $this->template->canUpgrade = $this->model->canUpgrade();
    }
  }
  
  /**
   * @param ManageMonasteryFormFactory $factory
   * @return Form
   */
  protected function createComponentManageMonasteryForm(ManageMonasteryFormFactory $factory) {
    $form = $factory->create($this->monasteryId);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function handleUpgrade() {
    try {
      $this->model->upgrade();
      $this->flashMessage("Klášter vylepšen.");
      $this->redirect("manage");
    } catch(CannotUpgradeMonasteryException $e) {
      $this->flashMessage("Nemůžeš vylepšit klášter.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
  
  /**
   * @return void
   */
  function handleRepair() {
    try {
      $this->model->repair();
      $this->flashMessage("Klášter opraven.");
      $this->redirect("manage");
    } catch(CannotRepairMonasteryException $e) {
      $this->flashMessage("Nemůžeš opravit klášter.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
}
?>