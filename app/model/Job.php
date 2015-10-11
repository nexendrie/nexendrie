<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Job as JobEntity,
    Nexendrie\Orm\UserJob as UserJobEntity;

/**
 * Job Model
 *
 * @author Jakub Konečný
 */
class Job extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all jobs
   * 
   * @return JobEntity[]
   */
  function listOfJobs() {
    return $this->orm->jobs->findAll();
  }
  
  /**
   * Find available jobs for user
   * 
   * @return JobEntity[]
   * @throws AuthenticationNeededException
   */
  function findAvailableJobs() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    else return $this->orm->jobs->findForLevel($this->user->identity->level);
  }
  
  /**
   * Get specified job's details
   * 
   * @param int $id
   * @return JobEntity
   * @throws JobNotFoundExceptions
   */
  function getJob($id) {
    $job = $this->orm->jobs->getById($id);
    if(!$job) throw new JobNotFoundExceptions("Specified job was not found.");
    else return $job;
  }
  
  /**
   * Add new job
   * 
   * @param array $data
   * @return void
   */
  function addJob(array $data) {
    $job = new JobEntity;
    foreach($data as $key => $value) {
      $job->$key = $value;
    }
    $this->orm->jobs->persistAndFlush($job);
  }
  
  /**
   * Edit specified job
   * 
   * @param int $id Job's id
   * @param array $data
   * @return void
   */
  function editJob($id, array $data) {
    $job = $this->orm->jobs->getById($id);
    foreach($data as $key => $value) {
      $job->$key = $value;
    }
    $this->orm->jobs->persistAndFlush($job);
  }
  
  /**
   * Start new job
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws AlreadyWorkingException
   * @throws JobNotFoundExceptions
   * @throws InsufficientLevelForJobException
   */
  function startJob($id) {
    if($this->isWorking()) throw new AlreadyWorkingException;
    $row = $this->orm->jobs->getById($id);
    if(!$row) throw new JobNotFoundExceptions;
    if($row->level > $this->user->identity->level) throw new InsufficientLevelForJobException;
    $job = new UserJobEntity;
    $this->orm->userJobs->attach($job);
    $job->user = $this->user->id;
    $job->job = $id;
    $job->started = time();
    $this->orm->userJobs->persistAndFlush($job);
    
  }
  
  /**
   * Finish job
   * 
   * @return int Reward
   */
  function finishJob() {
    
  }
  
  /**
   * Do one operation in job
   * 
   * @return array Results
   */
  function work() {
    
  }
  
  /**
   * Check whetever the use is currently working
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function isWorking() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $activeJob = $this->orm->userJobs->getUserActiveJob($this->user->id);
    if($activeJob) return true;
    else return false;
  }
}

class JobNotFoundExceptions extends RecordNotFoundException {
  
}

class AlreadyWorkingException extends AccessDeniedException {
  
}

class InsufficientLevelForJobException extends AccessDeniedException {
  
}
?>