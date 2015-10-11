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
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  
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
  function renderDefault() {
    $job = $this->model->getCurrentJob();
    $this->template->jobName = $job->job->name;
    $finishTime = $job->finishTime;
    $finished = ($finishTime < time());
    $this->template->finished = $finished;
    $this->template->finishTime = $this->localeModel->formatDateTime($finishTime);
    if(!$finished) {
      $this->template->help = $job->job->help;
      $this->template->canWork = $this->model->canWork();
    }
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
   * @return void
   */
  function actionFinish() {
    
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionWork() {
    
  }
}
?>