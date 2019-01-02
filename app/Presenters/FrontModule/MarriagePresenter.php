<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\ChangeWeddingTermFormFactory;
use Nexendrie\Model\CannotProposeMarriageException;
use Nexendrie\Model\MarriageNotFoundException;
use Nexendrie\Model\AccessDeniedException;
use Nexendrie\Model\MarriageProposalAlreadyHandledException;
use Nexendrie\Model\NotEngagedException;
use Nexendrie\Model\WeddingAlreadyHappenedException;
use Nexendrie\Model\NotMarriedException;
use Nexendrie\Model\AlreadyInDivorceException;
use Nexendrie\Model\NotInDivorceException;
use Nexendrie\Model\CannotTakeBackDivorceException;
use Nexendrie\Model\MaxIntimacyReachedException;
use Nexendrie\Model\ItemNotFoundException;
use Nexendrie\Model\ItemNotUsableException;
use Nexendrie\Model\ItemNotOwnedException;
use Nexendrie\Components\IWeddingControlFactory;
use Nexendrie\Components\WeddingControl;
use Nexendrie\Orm\Marriage as MarriageEntity;

/**
 * Presenter Marriage
 *
 * @author Jakub Konečný
 */
final class MarriagePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Marriage */
  protected $model;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  /** @var \Nexendrie\Model\Inventory */
  protected $inventoryModel;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var MarriageEntity */
  private $marriage;
  
  public function __construct(\Nexendrie\Model\Marriage $model, \Nexendrie\Model\Profile $profileModel, \Nexendrie\Model\Inventory $inventoryModel, \Nexendrie\Model\Locale $localeModel) {
    parent::__construct();
    $this->model = $model;
    $this->profileModel = $profileModel;
    $this->inventoryModel = $inventoryModel;
    $this->localeModel = $localeModel;
  }
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
  }
  
  public function actionDefault(): void {
    $marriage = $this->model->getCurrentMarriage();
    if(is_null($marriage)) {
      $this->redirect("proposals");
    }
    $this->template->marriage = $this->marriage = $marriage;
    $this->template->otherParty = ($marriage->user1->id === $this->user->id) ? $marriage->user2 : $marriage->user1;
    if($marriage->status === MarriageEntity::STATUS_ACTIVE) {
      $this->template->boosters = $this->inventoryModel->intimacyBoosters();
      $this->template->maxIntimacy = MarriageEntity::MAX_INTIMACY;
    }
  }
  
  public function actionPropose(int $id): void {
    try {
      $this->model->proposeMarriage($id);
      $this->flashMessage("Sňatek navržen.");
    } catch(CannotProposeMarriageException $e) {
      $this->flashMessage("Nemůžeš navrhnout sňatek.");
    }
    $this->redirect("Homepage:");
  }
  
  public function renderProposals(): void {
    $this->template->proposals = $this->model->listOfProposals();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionAccept(int $id): void {
    try {
      $this->model->acceptProposal($id);
      $this->flashMessage("Návrh přijat. Nyní jste zasnoubení.");
    } catch(MarriageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    } catch(CannotProposeMarriageException $e) {
      $this->flashMessage("Nemůžete se zasnoubit.");
    } catch(MarriageProposalAlreadyHandledException $e) {
      $this->flashMessage("Tento návrh byl již vyřízen.");
    } catch(AccessDeniedException $e) {
      $this->flashMessage("Nemůžeš přijmout tento návrh.");
    }
    $this->redirect("Homepage:");
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionDecline(int $id): void {
    try {
      $this->model->declineProposal($id);
      $this->flashMessage("Návrh zamítnut.");
    } catch(MarriageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    } catch(CannotProposeMarriageException $e) {
      $this->flashMessage("Nemůžete se zasnoubit.");
    } catch(MarriageProposalAlreadyHandledException $e) {
      $this->flashMessage("Tento návrh byl již vyřízen.");
    } catch(AccessDeniedException $e) {
      $this->flashMessage("Nemůžeš přijmout tento návrh.");
    }
    $this->redirect("Homepage:");
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionCeremony(int $id): void {
    try {
      $this->marriage = $this->model->getMarriage($id);
    } catch(MarriageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
    if($this->marriage->status != MarriageEntity::STATUS_ACCEPTED) {
      $this->flashMessage("Svatba se nekoná.");
      $this->redirect("Homepage:");
    } elseif($this->marriage->term > time()) {
      $this->flashMessage("Svatba ještě nezačala.");
      $this->redirect("Homepage:");
    } elseif($this->marriage->term + 60 * 60 < time()) {
      $this->flashMessage("Svatba už skončila.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentWedding(IWeddingControlFactory $factory): WeddingControl {
    $wedding = $factory->create();
    $wedding->marriage = $this->marriage;
    return $wedding;
  }
  
  public function handleCancelWedding(): void {
    try {
      $this->model->cancelWedding();
      $this->flashMessage("Zasnoubení zrušeno.");
      $this->redirect("default");
    } catch(NotEngagedException $e) {
      $message = $this->localeModel->genderMessage("Nejsi zasnouben(ý|á).");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(WeddingAlreadyHappenedException $e) {
      $this->flashMessage("Svatba se už uskutečnila.");
      $this->redirect("Homepage:");
    }
  }
  
  public function handleFileForDivorce(): void {
    try {
      $this->model->fileForDivorce();
      $this->flashMessage("Žádost podána.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      $message = $this->localeModel->genderMessage("Nejsi (ženatý|vdaná).");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(AlreadyInDivorceException $e) {
      $this->flashMessage("Už se rozvádíte.");
      $this->redirect("default");
    }
  }
  
  public function handleAcceptDivorce(): void {
    try {
      $this->model->acceptDivorce();
      $this->flashMessage("Vaše manželství skončilo.");
      $this->redirect("Homepage:");
    } catch(NotMarriedException $e) {
      $message = $this->localeModel->genderMessage("Nejsi (ženatý|vdaná).");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(NotInDivorceException $e) {
      $this->flashMessage("Nerozvádíte se.");
      $this->redirect("default");
    }
  }
  
  public function handleDeclineDivorce(): void {
    try {
      $this->model->declineDivorce();
      $this->flashMessage("Žádost zamítnuta.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      $message = $this->localeModel->genderMessage("Nejsi (ženatý|vdaná).");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(NotInDivorceException $e) {
      $this->flashMessage("Nerozvádíte se.");
      $this->redirect("default");
    }
  }
  
  public function handleTakeBackDivorce(): void {
    try {
      $this->model->takeBackDivorce();
      $this->flashMessage("Žádost stáhnuta.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      $message = $this->localeModel->genderMessage("Nejsi (ženatý|vdaná).");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(NotInDivorceException $e) {
      $this->flashMessage("Nerozvádíte se.");
      $this->redirect("default");
    } catch(CannotTakeBackDivorceException $e) {
      $message = $this->localeModel->genderMessage("Nepodal(a) jsi žádost o rozvod.");
      $this->flashMessage($message);
      $this->redirect("default");
    }
  }
  
  public function handleBoostIntimacy(int $item): void {
    try {
      $this->inventoryModel->boostIntimacy($item);
      $this->flashMessage("Věc použita.");
    } catch(NotMarriedException $e) {
      $message = $this->localeModel->genderMessage("Nejsi vdan(ý|á).");
      $this->flashMessage($message);
    } catch(ItemNotFoundException $e) {
      $this->flashMessage("Věc nenalezena.");
    } catch(ItemNotOwnedException $e) {
      $this->flashMessage("Zadaná věc ti nepatří.");
    } catch(ItemNotUsableException $e) {
      $this->flashMessage("Nemůžeš použít tuto věc.");
    } catch(MaxIntimacyReachedException $e) {
      $this->flashMessage("Nemůžeš už zvýšit důvěrnost.");
    }
    $this->redirect("default");
  }
  
  protected function createComponentChangeWeddingTermForm(ChangeWeddingTermFormFactory $factory): Form {
    $form = $factory->create($this->marriage);
    $form->onSuccess[] = function() {
      $this->flashMessage("Termín svatby změněn.");
    };
    return $form;
  }
}
?>