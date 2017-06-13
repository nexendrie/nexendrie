<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nette\Application\UI\Form,
    Nexendrie\Forms\ChangeWeddingTermFormFactory,
    Nexendrie\Model\CannotProposeMarriageException,
    Nexendrie\Model\MarriageNotFoundException,
    Nexendrie\Model\AccessDeniedException,
    Nexendrie\Model\MarriageProposalAlreadyHandledException,
    Nexendrie\Model\NotEngagedException,
    Nexendrie\Model\WeddingAlreadyHappenedException,
    Nexendrie\Model\NotMarriedException,
    Nexendrie\Model\AlreadyInDivorceException,
    Nexendrie\Model\NotInDivorceException,
    Nexendrie\Model\CannotTakeBackDivorceException,
    Nexendrie\Model\MaxIntimacyReachedException,
    Nexendrie\Model\ItemNotFoundException,
    Nexendrie\Model\ItemNotUsableException,
    Nexendrie\Model\ItemNotOwnedException,
    Nexendrie\Components\IWeddingControlFactory,
    Nexendrie\Components\WeddingControl,
    Nexendrie\Orm\Marriage as MarriageEntity,
    Nexendrie\Orm\User as UserEntity;

/**
 * Presenter Marriage
 *
 * @author Jakub Konečný
 */
class MarriagePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Marriage @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  /** @var \Nexendrie\Model\Inventory @autowire */
  protected $inventoryModel;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  /** @var MarriageEntity */
  private $marriage;
  
  /**
   * @return void
   */
  function actionDefault(): void {
    $partner = $this->profileModel->getPartner($this->user->id);
    $fiance = $this->profileModel->getFiance($this->user->id);
    if(!$partner AND !$fiance) {
      $this->redirect("proposals");
    }
    $this->template->partner = $partner;
    $this->template->fiance = $fiance;
    $this->template->marriage = $this->marriage = $this->model->getCurrentMarriage();
    if(!is_null($partner)) {
      $this->template->boosters = $this->inventoryModel->intimacyBoosters();
      $this->template->maxIntimacy = MarriageEntity::MAX_INTIMACY;
    }
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionPropose(int $id): void {
    try {
      $this->model->proposeMarriage($id);
      $this->flashMessage("Sňatek navržen.");
    } catch(CannotProposeMarriageException $e) {
      $this->flashMessage("Nemůžeš navrhnout sňatek.");
    }
    $this->redirect("Homepage:");
  }
  
  /**
   * @return void
   */
  function renderProposals(): void {
    $this->requiresLogin();
    $this->template->proposals = $this->model->listOfProposals();
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionAccept(int $id): void {
    try {
      $this->model->acceptProposal($id);
      $this->flashMessage("Návrh přijat. Nyní jste zasnoubení.");
    } catch(MarriageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    } catch(CannotProposeMarriageException $e) {
      $this->flashMessage("Nemůžete se zasnoubit.");
    } catch(AccessDeniedException $e) {
      $this->flashMessage("Nemůžeš přijmout tento návrh.");
    } catch(MarriageProposalAlreadyHandledException $e) {
      $this->flashMessage("Tento návrh byl již vyřízen.");
    }
    $this->redirect("Homepage:");
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionDecline(int $id): void {
    try {
      $this->model->declineProposal($id);
      $this->flashMessage("Návrh zamítut.");
    } catch(MarriageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    } catch(CannotProposeMarriageException $e) {
      $this->flashMessage("Nemůžete se zasnoubit.");
    } catch(AccessDeniedException $e) {
      $this->flashMessage("Nemůžeš přijmout tento návrh.");
    } catch(MarriageProposalAlreadyHandledException $e) {
      $this->flashMessage("Tento návrh byl již vyřízen.");
    }
    $this->redirect("Homepage:");
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionCeremony(int $id): void {
    try {
      $this->marriage = $this->model->getMarriage($id);
    } catch(MarriageNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
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
  
  /**
   * @param IWeddingControlFactory $factory
   * @return WeddingControl
   */
  protected function createComponentWedding(IWeddingControlFactory $factory): WeddingControl {
    $wedding = $factory->create();
    $wedding->marriage = $this->marriage;
    return $wedding;
  }
  
  /**
   * @return void
   */
  function handleCancelWedding(): void {
    try {
      $this->model->cancelWedding();
      $this->flashMessage("Zasnoubení zrušeno.");
      $this->redirect("default");
    } catch(NotEngagedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Nejsi zasnoubená.";
      } else {
        $message = "Nejsi zasnoubený.";
      }
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(WeddingAlreadyHappenedException $e) {
      $this->flashMessage("Svatba se už uskutečnila.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function handleFileForDivorce(): void {
    try {
      $this->model->fileForDivorce();
      $this->flashMessage("Žádost podána.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Nejsi vdaná.";
      } else {
        $message = "Nejsi ženatý.";
      }
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(AlreadyInDivorceException $e) {
      $this->flashMessage("Už se rozvádíte.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function handleAcceptDivorce(): void {
    try {
      $this->model->acceptDivorce();
      $this->flashMessage("Vaše manželství skončilo.");
      $this->redirect("Homepage:");
    } catch(NotMarriedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Nejsi vdaná.";
      } else {
        $message = "Nejsi ženatý.";
      }
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(NotInDivorceException $e) {
      $this->flashMessage("Nerozvádíte se.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function handleDeclineDivorce(): void {
    try {
      $this->model->declineDivorce();
      $this->flashMessage("Žádost zamítnuta.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Nejsi vdaná.";
      } else {
        $message = "Nejsi ženatý.";
      }
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(NotInDivorceException $e) {
      $this->flashMessage("Nerozvádíte se.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function handleTakeBackDivorce(): void {
    try {
      $this->model->takeBackDivorce();
      $this->flashMessage("Žádost stáhnuta.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Nejsi vdaná.";
      } else {
        $message = "Nejsi ženatý.";
      }
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
  
  /**
   * @param int $item
   * @return void
   */
  function handleBoostIntimacy(int $item): void {
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
  
  /**
   * @param ChangeWeddingTermFormFactory $factory
   * @return Form
   */
  protected function createComponentChangeWeddingTermForm(ChangeWeddingTermFormFactory $factory): Form {
    $form = $factory->create($this->marriage);
    $form->onSuccess[] = function() {
      $this->flashMessage("Termín svatby změněn.");
    };
    return $form;
  }
}
?>