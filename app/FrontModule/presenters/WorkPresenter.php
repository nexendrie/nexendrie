<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\AlreadyWorkingException,
    Nexendrie\Model\JobNotFoundException,
    Nexendrie\Model\InsufficientLevelForJobException,
    Nexendrie\Model\NotWorkingException,
    Nexendrie\Model\CannotWorkException,
    Nexendrie\Model\JobNotFinishedException,
    Nexendrie\Model\InsufficientSkillLevelForJobException;

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
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
    $this->mustNotBeTavelling();
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
    $this->template->jobCount = $job->count;
    $this->template->jobNeededCount = $job->job->count;
    $finishTime = $job->finishTime;
    $finished = ($finishTime < time());
    $this->template->finished = $finished;
    $this->template->finishTime = $this->localeModel->formatDateTime($finishTime);
    $earned = $this->model->calculateReward($job);
    $this->template->earned = $this->localeModel->money(array_sum($earned));
    if(!$finished) {
      $this->template->help = $this->model->parseJobHelp($job);
      $this->template->canWork = $this->model->canWork();
      $nextShift = $job->lastAction + ($job->job->shift * 60);
      $this->template->nextShift = $this->localeModel->formatDateTime($nextShift);
    } else {
      $this->template->canWork = false;
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
    } catch(JobNotFoundException $e) {
      $this->flashMessage("Práce nenalezena.");
    } catch(InsufficientLevelForJobException $e) {
      $this->flashMessage("Nemáš dostatečnou úroveň pro tuto práci.");
    } catch(InsufficientSkillLevelForJobException $e) {
      $this->flashMessage("Neovládáš potřebnou dovednost pro tuto práci.");
    }
    $this->redirect("default");
  }
  
  /**
   * @return void
   */
  function actionFinish() {
    try {
      $rewards = $this->model->finishJob();
      $this->template->reward = $this->localeModel->money($rewards["reward"]);
      if($rewards["extra"]) $this->template->extra = $this->localeModel->money($rewards["extra"]);
      else $this->template->extra = false;
    } catch(NotWorkingException $e) {
      $this->flashMessage("Právě nevykonáváš žádnou práci.");
      $this->redirect("default");
    } catch(JobNotFinishedException $e) {
      $this->flashMessage("Práce ještě není hotova.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function actionWork() {
    try {
      $result = $this->model->work();
      $this->flashMessage($result->message);
    } catch(NotWorkingException $e) {
      $this->flashMessage("Právě nevykonáváš žádnou práci.");
    } catch(CannotWorkException $e) {
      $this->flashMessage("Ještě si nedokončil směnu.");
    }
    $this->redirect("default");
  }
}
?>