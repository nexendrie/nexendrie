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
  const BASE_SUCCESS_RATE = 55;
  
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
   * @throws JobNotFoundException
   */
  function getJob($id) {
    $job = $this->orm->jobs->getById($id);
    if(!$job) throw new JobNotFoundException("Specified job was not found.");
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
   * @throws JobNotFoundException
   * @throws InsufficientLevelForJobException
   */
  function startJob($id) {
    if($this->isWorking()) throw new AlreadyWorkingException;
    $row = $this->orm->jobs->getById($id);
    if(!$row) throw new JobNotFoundException;
    if($row->level > $this->user->identity->level) throw new InsufficientLevelForJobException;
    $job = new UserJobEntity;
    $this->orm->userJobs->attach($job);
    $job->user = $this->user->id;
    $job->job = $id;
    $job->started = time();
    $this->orm->userJobs->persistAndFlush($job);
    
  }
  
  /**
   * Calculate reward from job
   * 
   * @param UserJobEntity $job
   * @return int[] Reward
   */
  function calculateReward(UserJobEntity $job) {
    $reward = $extra = 0;
    if($job->job->count === 0) {
      $reward += $job->job->award * $job->count;
    } else {
      if($job->count < $job->job->count) {
        $part = $job->job->count / $job->count;
        $reward += (int) ($job->job->award / $part);
      } else {
        $reward += $job->job->award;
        if($job->count > $job->job->count) {
          $extra += (int) ($job->job->award / 5);
        }
      }
    }
    return array("reward" => $reward, "extra" => $extra);
  }
  
  /**
   * Finish job
   * 
   * @return int[] Reward
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   * @throws JobNotFinishedException
   */
  function finishJob() {
    try {
      $currentJob = $this->getCurrentJob();
    } catch(AccessDeniedException $e) {
      throw $e;
    }
    if(time() < $currentJob->finishTime) throw new JobNotFinishedException;
    $rewards = $this->calculateReward($currentJob);
    $currentJob->finished = 1;
    $currentJob->earned = $rewards["reward"];
    $currentJob->extra = $rewards["extra"];
    $this->orm->userJobs->persist($currentJob);
    $user = $this->orm->users->getById($this->user->id);
    $user->money += $rewards["reward"] + $rewards["extra"];
    $this->orm->users->persist($user);
    $this->orm->flush();
    return $rewards;
  }
  
  /**
   * Do one operation in job
   * @todo Read result messages from database
   * 
   * @return \stdClass Results
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   * @throws CannotWorkException
   */
  function work() {
    try {
      $canWork = $this->canWork();
      if(!$canWork) throw new CannotWorkException;
    } catch(AccessDeniedException $e) {
      throw $e;
    }
    $job = $this->getCurrentJob();
    $success = (rand(1, 100) <= self::BASE_SUCCESS_RATE);
    if($success) $job->count++;
    $job->lastAction = time();
    $this->orm->userJobs->persistAndFlush($job);
    if($success) $message = "Úspěšně jsi zvládl směnu.";
    else $message = "Nezvládl jsi tuto směnu.";
    $result = (object) array(
      "success" => $success , "message" => $message
    );
    return $result;
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
  
  /**
   * Get user's current job
   * 
   * @return UserJobEntity
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   */
  function getCurrentJob() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $job = $this->orm->userJobs->getUserActiveJob($this->user->id);
    if(!$job) throw new NotWorkingException;
    else return $job;
  }
  
  /**
   * Check whetever user can work now
   * 
   * @return bool
   * @throws AuthenticationNeededException
   * @throws NotWorkingException
   */
  function canWork() {
    try {
      $job = $this->getCurrentJob();
    } catch(AccessDeniedException $e) {
      throw $e;
    }
    if($job->lastAction === NULL) return true;
    elseif($job->lastAction + ($job->job->shift * 60) > time()) return false;
    else return true;
  }
}

class JobNotFoundException extends RecordNotFoundException {
  
}

class AlreadyWorkingException extends AccessDeniedException {
  
}

class InsufficientLevelForJobException extends AccessDeniedException {
  
}

class NotWorkingException extends AccessDeniedException {
  
}

class CannotWorkException extends AccessDeniedException {
  
}

class JobNotFinishedException extends AccessDeniedException {
  
}
?>