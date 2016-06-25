<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CannotProposeMarriageException,
    Nexendrie\Model\MarriageNotFoundException,
    Nexendrie\Model\AccessDeniedException,
    Nexendrie\Model\MarriageProposalAlreadyHandledException,
    Nexendrie\Model\NotEngagedException,
    Nexendrie\Model\WeddingAlreadyHappenedException,
    Nexendrie\Model\NotMarriedException,
    Nexendrie\Model\AlreadyInDivorceException,
    Nexendrie\Model\NotInDivorceException,
    Nexendrie\Components\WeddingControlFactory,
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
  /** @var MarriageEntity */
  private $marriage;
  
  /**
   * @return void
   */
  function actionDefault() {
    $partner = $this->profileModel->getPartner($this->user->id);
    $fiance = $this->profileModel->getFiance($this->user->id);
    if(!$partner AND !$fiance) $this->redirect("proposals");
    $this->template->partner = $partner;
    $this->template->fiance = $fiance;
    $this->template->marriage = $this->model->getCurrentMarriage();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionPropose($id) {
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
  function renderProposals() {
    $this->requiresLogin();
    $this->template->proposals = $this->model->listOfProposals();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionAccept($id) {
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
   */
  function actionDecline($id) {
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
   */
  function actionCeremony($id) {
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
   * @param WeddingControlFactory $factory
   * @return WeddingControl
   */
  protected function createComponentWedding(WeddingControlFactory $factory) {
    $wedding = $factory->create();
    $wedding->marriage = $this->marriage;
    return $wedding;
  }
  
  /**
   * @return void
   */
  function handleCancelWedding() {
    try {
      $this->model->cancelWedding();
      $this->flashMessage("Zasnoubení zrušeno.");
      $this->redirect("default");
    } catch(NotEngagedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Nejsi zasnoubená.";
      else $message = "Nejsi zasnoubený.";
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
  function handleFileForDivorce() {
    try {
      $this->model->fileForDivorce();
      $this->flashMessage("Žádost podána.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Nejsi vdaná.";
      else $message = "Nejsi ženatý.";
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
  function handleAcceptDivorce() {
    try {
      $this->model->acceptDivorce();
      $this->flashMessage("Vaše manželství skončilo.");
      $this->redirect("Homepage:");
    } catch(NotMarriedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Nejsi vdaná.";
      else $message = "Nejsi ženatý.";
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
  function handleDeclineDivorce() {
    try {
      $this->model->declineDivorce();
      $this->flashMessage("Žádost zamítnuta.");
      $this->redirect("default");
    } catch(NotMarriedException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Nejsi vdaná.";
      else $message = "Nejsi ženatý.";
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(NotInDivorceException $e) {
      $this->flashMessage("Nerozvádíte se.");
      $this->redirect("default");
    }
  }
}
?>