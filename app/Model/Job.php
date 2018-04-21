<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Job as JobEntity,
    Nexendrie\Orm\UserJob as UserJobEntity,
    Nexendrie\Orm\JobMessage as JobMessageEntity,
    Nextras\Orm\Collection\ICollection,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Job Model
 *
 * @author Jakub Konečný
 */
class Job {
  /** @var Skills */
  protected $skillsModel;
  /** @var Locale @autowire */
  protected $localeModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** Base success rate for job (in %) */
  public const BASE_SUCCESS_RATE = 55;
  
  use \Nette\SmartObject;
  
  public function __construct(Skills $skillsModel, Locale $localeModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->skillsModel = $skillsModel;
    $this->localeModel = $localeModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all jobs
   * 
   * @return JobEntity[]|ICollection
   */
  public function listOfJobs(): ICollection {
    return $this->orm->jobs->findAll();
  }
  
  /**
   * Calculate reward from an offer
   *
   * @throws AuthenticationNeededException
   */
  public function calculateAward(JobEntity $offer): \stdClass {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $oldAward = $offer->award;
    $job = new UserJobEntity();
    $job->job = $offer;
    $job->user = $this->orm->users->getById($this->user->id);
    $job->count = ($job->job->count > 0) ? $job->job->count : 1;
    $offer->award = array_sum($job->reward);
    $o = (object) $offer->toArray();
    $o->award = $offer->awardT;
    $offer->award = $oldAward;
    return $o;
  }
  
  /**
   * Find available jobs for user
   * 
   * @return \stdClass[]
   * @throws AuthenticationNeededException
   */
  public function findAvailableJobs(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $return = [];
    $offers = $this->orm->jobs->findForLevel($this->user->identity->level);
    foreach($offers as $offer) {
      if($offer->neededSkillLevel > 0) {
        $userSkillLevel = $this->skillsModel->getLevelOfSkill($offer->neededSkill->id);
        if($userSkillLevel < $offer->neededSkillLevel) {
          continue;
        }
      }
      $return[] = $this->calculateAward($offer);
    }
    return $return;
  }
  
  /**
   * Get specified job's details
   *
   * @throws JobNotFoundException
   */
  public function getJob(int $id): JobEntity {
    $job = $this->orm->jobs->getById($id);
    if(is_null($job)) {
      throw new JobNotFoundException("Specified job was not found.");
    }
    return $job;
  }
  
  /**
   * Add new job
   */
  public function addJob(array $data): void {
    $job = new JobEntity();
    $this->orm->jobs->attach($job);
    foreach($data as $key => $value) {
      $job->$key = $value;
    }
    $this->orm->jobs->persistAndFlush($job);
  }
  
  /**
   * Edit specified job
   *
   * @throws JobNotFoundException
   */
  public function editJob(int $id, array $data): void {
    try {
      $job = $this->getJob($id);
    } catch(JobNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $job->$key = $value;
    }
    $this->orm->jobs->persistAndFlush($job);
  }
  
  /**
   * Start new job
   *
   * @throws AuthenticationNeededException
   * @throws AlreadyWorkingException
   * @throws JobNotFoundException
   * @throws InsufficientLevelForJobException
   */
  public function startJob(int $id): void {
    if($this->isWorking()) {
      throw new AlreadyWorkingException();
    }
    $row = $this->orm->jobs->getById($id);
    if(is_null($row)) {
      throw new JobNotFoundException();
    }
    if($row->level > $this->user->identity->level) {
      throw new InsufficientLevelForJobException();
    }
    if($row->neededSkillLevel > 0) {
      $userSkillLevel = $this->skillsModel->getLevelOfSkill($row->neededSkill->id);
      if($userSkillLevel < $row->neededSkillLevel) {
        throw new InsufficientSkillLevelForJobException();
      }
    }
    $job = new UserJobEntity();
    $this->orm->userJobs->attach($job);
    $job->user = $this->user->id;
    $job->job = $id;
    $this->orm->userJobs->persistAndFlush($job);
    
  }
  
  /**
   * Finish job
   * 
   * @return int[] Reward
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   * @throws JobNotFinishedException
   */
  public function finishJob(): array {
    try {
      $currentJob = $this->getCurrentJob();
    } catch(AccessDeniedException $e) {
      throw $e;
    }
    if(time() < $currentJob->finishTime) {
      throw new JobNotFinishedException();
    }
    $rewards = $currentJob->reward;
    $currentJob->finished = true;
    $currentJob->earned = $rewards["reward"];
    $currentJob->extra = $rewards["extra"];
    $currentJob->user->money += array_sum($rewards);
    $this->orm->userJobs->persistAndFlush($currentJob);
    return $rewards;
  }
  
  /**
   * Get result message
   */
  public function getResultMessage(int $job, bool $success): string {
    $messages = $this->orm->jobMessages->findByJobAndSuccess($job, $success);
    if($messages->count() === 0 AND $success) {
      return $this->localeModel->genderMessage("Úspěšně jsi zvládl(a) směnu.");
    } elseif($messages->count() === 0 AND !$success) {
      return $this->localeModel->genderMessage("Nezvládl(a) jsi tuto směnu.");
    }
    $message = "";
    $roll = rand(0, $messages->count() - 1);
    $i = 0;
    /** @var JobMessageEntity $m */
    foreach($messages->fetchAll() as $m) {
      if($i === $roll) {
        $message = $m->message;
      }
      $i++;
    }
    return $message;
  }
  
  /**
   * Do one operation in job
   *
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   * @throws CannotWorkException
   */
  public function work(): \stdClass {
    try {
      $canWork = $this->canWork();
      if(!$canWork) {
        throw new CannotWorkException();
      }
    } catch(AccessDeniedException $e) {
      throw $e;
    }
    $job = $this->getCurrentJob();
    $success = (rand(1, 100) <= $job->successRate);
    if($success) {
      $job->count++;
    }
    $job->lastAction = time();
    $this->orm->userJobs->persistAndFlush($job);
    $message = $this->getResultMessage($job->job->id, $success);
    $result = (object) [
      "success" => $success , "message" => $message
    ];
    return $result;
  }
  
  /**
   * Check whether the use is currently working
   *
   * @throws AuthenticationNeededException
   */
  public function isWorking(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $activeJob = $this->orm->userJobs->getUserActiveJob($this->user->id);
    return !(is_null($activeJob));
  }
  
  /**
   * Get user's current job
   *
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   */
  public function getCurrentJob(): UserJobEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $job = $this->orm->userJobs->getUserActiveJob($this->user->id);
    if(is_null($job)) {
      throw new NotWorkingException();
    }
    return $job;
  }
  
  /**
   * Parse job's help text
   */
  public function parseJobHelp(UserJobEntity $job): string {
    $oldCount = $job->count;
    $job->count = ($job->job->count > 0) ? $job->job->count : 1;
    $reward = $this->localeModel->money(array_sum($job->reward));
    $job->count = $oldCount;
    $help = str_replace("%reward%", $reward, $job->job->help);
    $help = str_replace("%count%", $job->job->count, $help);
    return $help;
  }
  
  /**
   * Check whether user can work now
   *
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   */
  public function canWork(): bool {
    try {
      $job = $this->getCurrentJob();
    } catch(AccessDeniedException $e) {
      throw $e;
    }
    if(is_null($job->lastAction)) {
      return true;
    } elseif($job->lastAction + ($job->job->shift * 60) > time()) {
      return false;
    }
    return true;
  }
  
  /**
   * Get messages for specified job
   *
   * @return JobMessageEntity[]|OneHasMany
   * @throws JobNotFoundException
   */
  public function listOfMessages(int $jobId): OneHasMany {
    $job = $this->orm->jobs->getById($jobId);
    if(is_null($job)) {
      throw new JobNotFoundException();
    }
    return $job->messages;
  }
  
  /**
   * Get specified job message
   *
   * @throws JobMessageNotFoundException
   */
  public function getMessage(int $id): JobMessageEntity {
    $message = $this->orm->jobMessages->getById($id);
    if(is_null($message)) {
      throw new JobMessageNotFoundException();
    }
    return $message;
  }
  
  /**
   * Add new job message
   */
  public function addMessage(array $data): void {
    $message = new JobMessageEntity();
    $this->orm->jobMessages->attach($message);
    foreach($data as $key => $value) {
      if($key === "success") {
        $value = (bool) $value;
      }
      $message->$key = $value;
    }
    $this->orm->jobMessages->persistAndFlush($message);
  }
  
  /**
   * Edit specified job message
   *
   * @throws JobMessageNotFoundException
   */
  public function editMessage(int $id, array $data): void {
    try {
      $message = $this->getMessage($id);
    } catch(JobMessageNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      if($key === "success") {
        $value = (bool) $value;
      }
      $message->$key = $value;
    }
    $this->orm->jobMessages->persistAndFlush($message);
  }
  
  /**
   * Remove specified job message
   *
   * @throws JobMessageNotFoundException
   */
  public function deleteMessage(int $id): int {
    $message = $this->orm->jobMessages->getById($id);
    if(is_null($message)) {
      throw new JobMessageNotFoundException();
    }
    $return = $message->job->id;
    $this->orm->jobMessages->remove($message);
    return $return;
  }
  
  /**
   * Calculate income from user's jobs from a month
   */
  public function calculateMonthJobIncome(int $userId = NULL, int $month = NULL, int $year = NULL): int {
    $income = 0;
    $jobs = $this->orm->userJobs->findFromMonth($userId ?? $this->user->id, $month, $year);
    foreach($jobs as $job) {
      $income += array_sum($job->reward);
    }
    return $income;
  }
}
?>