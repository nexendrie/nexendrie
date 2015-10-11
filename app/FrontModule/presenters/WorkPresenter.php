<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Model\AlreadyWorkingException,
    Nexendrie\Model\JobNotFoundExceptions,
    Nexendrie\Model\InsufficientLevelForJobException;

/**
 * Presenter Work
 *
 * @author Jakub Konečný
 */
class WorkPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Job @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function actionDefault() {
    if(!$this->model->isWorking()) $this->redirect("offers");
  }
  
  /**
   * @return void
   */
  function actionOffers() {
    if($this->model->isWorking()) {
      $this->flashMessage("Už pracuješ.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function renderOffers() {
    $this->template->offers = $this->model->findAvailableJobs();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionStart($id) {
    try {
      $this->model->startJob($id);
      $this->flashMessage("Práce zahájena.");
    } catch(AlreadyWorkingException $e) {
      $this->flashMessage("Už pracuješ.");
    } catch(JobNotFoundExceptions $e) {
      $this->flashMessage("Práce nenalezena.");
    } catch(InsufficientLevelForJobException $e) {
      $this->flashMessage("Nemáš dostatečnou úroveň pro tuto práci.");
    }
    $this->redirect("default");
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionFinish($id) {
    
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionWork() {
    
  }
}
?>