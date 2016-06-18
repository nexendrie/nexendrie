<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CannotProposeMarriageException,
    Nexendrie\Model\MarriageNotFoundException,
    Nexendrie\Model\AccessDeniedException,
    Nexendrie\Model\MarriageProposalAlreadyHandledException;

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
  
  /**
   * @return void
   */
  function actionDefault() {
    if(!$this->profileModel->getPartner($this->user->id) AND !$this->profileModel->getFiance($this->user->id)) $this->redirect("proposals");
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
}
?>