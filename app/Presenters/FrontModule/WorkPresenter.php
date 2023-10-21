<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\AlreadyWorkingException;
use Nexendrie\Model\JobNotFoundException;
use Nexendrie\Model\InsufficientLevelForJobException;
use Nexendrie\Model\NotWorkingException;
use Nexendrie\Model\CannotWorkException;
use Nexendrie\Model\JobNotFinishedException;
use Nexendrie\Model\InsufficientSkillLevelForJobException;

/**
 * Presenter Work
 *
 * @author Jakub Konečný
 */
final class WorkPresenter extends BasePresenter {
  protected \Nexendrie\Model\Job $model;
  protected \Nexendrie\Model\Locale $localeModel;
  protected bool $cachingEnabled = false;
  
  public function __construct(\Nexendrie\Model\Job $model, \Nexendrie\Model\Locale $localeModel) {
    parent::__construct();
    $this->model = $model;
    $this->localeModel = $localeModel;
  }
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
    $this->mustNotBeTavelling();
  }
  
  public function actionDefault(): void {
    if(!$this->model->isWorking()) {
      $this->redirect("offers");
    }
  }
  
  public function renderDefault(): void {
    $job = $this->model->getCurrentJob();
    $this->template->jobName = $job->job->name;
    $this->template->jobCount = $job->count;
    $this->template->jobNeededCount = $job->job->count;
    $finishTime = $job->finishTime;
    $finished = ($finishTime < time());
    $this->template->finished = $finished;
    $this->template->finishTime = $this->localeModel->formatDateTime($finishTime);
    $earned = $job->reward;
    $this->template->earned = (int) array_sum($earned);
    if(!$finished) {
      $this->template->help = $this->model->parseJobHelp($job);
      $this->template->canWork = $this->model->canWork();
      $nextShift = ((int) $job->lastAction) + ($job->job->shift * 60);
      $this->template->nextShift = $this->localeModel->formatDateTime($nextShift);
      $this->template->nextShiftJs = date("Y-m-d", $nextShift) . "T" . date("H:i:s", $nextShift);
      $this->template->successChance = $job->successRate;
    } else {
      $this->template->canWork = false;
    }
  }
  
  public function actionOffers(): void {
    if($this->model->isWorking()) {
      $this->flashMessage("Už pracuješ.");
      $this->redirect("default");
    }
  }
  
  public function renderOffers(): void {
    $this->template->offers = $this->model->findAvailableJobs();
  }
  
  public function actionStart(int $id): void {
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
  
  public function actionFinish(): void {
    try {
      $rewards = $this->model->finishJob();
      $this->template->reward = $rewards["reward"];
      if($rewards["extra"] > 0) {
        $this->template->extra = $rewards["extra"];
      } else {
        $this->template->extra = false;
      }
    } catch(NotWorkingException $e) {
      $this->flashMessage("Právě nevykonáváš žádnou práci.");
      $this->redirect("default");
    } catch(JobNotFinishedException $e) {
      $this->flashMessage("Práce ještě není hotova.");
      $this->redirect("default");
    }
  }
  
  public function actionWork(): void {
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