<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CannotProposeMarriageException,
    Nexendrie\Model\MarriageNotFoundException,
    Nexendrie\Model\AccessDeniedException,
    Nexendrie\Model\MarriageProposalAlreadyHandledException,
    Nexendrie\Components\WeddingControlFactory,
    Nexendrie\Components\WeddingControl,
    Nexendrie\Orm\Marriage as MarriageEntity;

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
}
?>