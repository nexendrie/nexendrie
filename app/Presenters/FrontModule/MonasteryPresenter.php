<?php
declare(strict_types=1);

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
    Nexendrie\Model\CannotRepairMonasteryException;

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
  
  protected function startup(): void {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") {
      $this->requiresLogin();
    }
  }
  
  function renderDefault(): void {
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
  
  function renderList(): void {
    $this->template->monasteries = $this->model->listOfMonasteries();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  function renderDetail(int $id): void {
    try {
      $this->template->monastery = $this->model->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  function actionBuild(): void {
    if(!$this->model->canBuild()) {
      $this->flashMessage("Nemůžeš postavit klášter.");
      $this->redirect("Homepage:");
    }
    $this->template->buildingPrice = $this->localeModel->money($this->model->buildingPrice);
  }
  
  protected function createComponentBuildMonasteryForm(BuildMonasteryFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[]= function(Form $form) {
      $this->flashMessage("Klášter založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  function actionJoin(int $id): void {
    try {
      $this->model->join($id);
      $message = $this->localeModel->genderMessage("Vstoupil(a) jsi do kláštera");
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
  
  function actionLeave(): void {
    try {
      $this->model->leave();
      $message = $this->localeModel->genderMessage("Vystoupil(a) jsi z kláštera");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveMonasteryException $e) {
      $this->flashMessage("Nemůžeš vystoupit z kláštera.");
      $this->redirect("default");
    }
  }
  
  function actionPray(): void {
    try {
      $this->model->pray();
      $this->flashMessage("Modlidba ti přidala 5 životů.");
      $this->redirect("default");
    } catch(CannotPrayException $e) {
      $this->flashMessage("Nemůžeš se modlit (právě teď).");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentMonasteryDonateForm(MonasteryDonateFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Příspěvek proveden.");
    };
    return $form;
  }
  
  function actionManage(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat klášter.");
      $this->redirect("Homepage:");
    }
    $this->template->monastery = $monastery = $this->model->getByUser();
    $this->monasteryId = $monastery->id;
    $this->template->canUpgrade = $this->model->canUpgrade();
  }
  
  protected function createComponentManageMonasteryForm(ManageMonasteryFormFactory $factory): Form {
    $form = $factory->create($this->monasteryId);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  function handleUpgrade(): void {
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
  
  function handleRepair(): void {
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