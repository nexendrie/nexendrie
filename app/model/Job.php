<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Job as JobEntity,
    Nexendrie\Orm\UserJob as UserJobEntity,
    Nexendrie\Orm\JobMessage as JobMessageEntity;

/**
 * Job Model
 *
 * @author Jakub Konečný
 */
class Job extends \Nette\Object {
  /** @var Skills */
  protected $skillsModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** Base success rate for job (in %) */
  const BASE_SUCCESS_RATE = 55;
  
  function __construct(Skills $skillsModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->skillsModel = $skillsModel;
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
    $return = array();
    $offers = $this->orm->jobs->findForLevel($this->user->identity->level);
    foreach($offers as $offer) {
      if($offer->neededSkillLevel > 0) {
        $userSkillLevel = $this->skillsModel->getLevelOfSkill($offer->neededSkill->id);
        if($userSkillLevel < $offer->neededSkillLevel) continue;
      }
      $return[] = $offer;
    }
    return $return;
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
    $this->orm->jobs->attach($job);
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
    if($row->neededSkillLevel > 0) {
        $userSkillLevel = $this->skillsModel->getLevelOfSkill($row->neededSkill->id);
        if($userSkillLevel < $row->neededSkillLevel) throw new InsufficientSkillLevelForJobException;
      }
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
    if($job->finished) return array("reward" => $job->earned, "extra" => $job->extra);
    $reward = $extra = 0;
    if($job->job->count === 0) {
      $reward += $job->job->award * $job->count;
    } else {
      if($job->count < $job->job->count) {
        $part = @($job->job->count / $job->count);
        $reward += (int) @($job->job->award / $part);
      } else {
        $reward += $job->job->award;
        if($job->count > $job->job->count) {
          $extra += (int) ($job->job->award / 5);
        }
      }
    }
    $extra += $this->skillsModel->calculateSkillIncomeBonus($reward, $job->job->neededSkill->id);
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
    $currentJob->finished = true;
    $currentJob->earned = (int) round($rewards["reward"]);
    $currentJob->extra = (int) round($rewards["extra"]);
    $currentJob->user->money += array_sum($rewards);
    $this->orm->userJobs->persistAndFlush($currentJob);
    return $rewards;
  }
  
  /**
   * Get result message
   * 
   * @param int $job
   * @param bool $success
   * @return string
   */
  function getResultMessage($job, $success) {
    $messages = $this->orm->jobMessages->findByJobAndSuccess($job, $success);
    if($messages->count() === 0 AND $success === 1) {
      $message = "Úspěšně jsi zvládl směnu.";
    } elseif($messages->count() === 0 AND $success === 0) {
      $message = "Nezvládl jsi tuto směnu.";
    } else {
      $roll = rand(0, $messages->count() - 1);
      $i = 0;
      foreach($messages->fetchAll() as $m) {
        if($i === $roll) $message = $m->message;
        $i++;
      }
    }
    return $message;
  }
  
  /**
   * Calculate success rate for job
   * 
   * @param UserJobEntity $job
   * @return int
   */
  function calculateSuccessRate(UserJobEntity $job) {
    $successRate = self::BASE_SUCCESS_RATE;
    $successRate += $this->skillsModel->calculateSkillSuccessBonus($job->job->neededSkill->id);
    return $successRate;
  }
  
  /**
   * Do one operation in job
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
    $success = (rand(1, 100) <= $this->calculateSuccessRate($job));
    if($success) $job->count++;
    $job->lastAction = time();
    $this->orm->userJobs->persistAndFlush($job);
    $message = $this->getResultMessage($job->job->id, $success);
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
  
  /**
   * Get messages for specified job
   * 
   * @param int $jobId
   * @return JobMessageEntity[]
   * @throws JobNotFoundException
   */
  function listOfMessages($jobId) {
    $job = $this->orm->jobs->getById($jobId);
    if(!$job) throw new JobNotFoundException;
    else return $job->messages;
  }
  
  /**
   * Get specified job message
   * 
   * @param int $id
   * @return JobMessageEntity
   * @throws JobMessageNotFoundException
   */
  function getMessage($id) {
    $message = $this->orm->jobMessages->getById($id);
    if(!$message) throw new JobMessageNotFoundException;
    else return $message;
  }
  
  /**
   * Add new job message
   * 
   * @param array $data
   * @return void
   */
  function addMessage(array $data) {
    $message = new JobMessageEntity;
    $this->orm->jobMessages->attach($message);
    foreach($data as $key => $value) {
      if($key === "success") $value = (int) $value;
      $message->$key = $value;
    }
    $this->orm->jobMessages->persistAndFlush($message);
  }
  
  /**
   * Edit specified job message
   * 
   * @param int $id Message's id
   * @param array $data
   * @return void
   */
  function editMessage($id, array $data) {
    $message = $this->orm->jobMessages->getById($id);
    foreach($data as $key => $value) {
      if($key === "success") $value = (int) $value;
      $message->$key = $value;
    }
    $this->orm->jobMessages->persistAndFlush($message);
  }
  
  /**
   * Remove specified job message
   * 
   * @param int $id
   * @return int
   * @throws JobMessageNotFoundException
   */
  function deleteMessage($id) {
    $message = $this->orm->jobMessages->getById($id);
    if(!$message) {
      throw new JobMessageNotFoundException;
    } else {
      $return = $message->job->id;
      $this->orm->jobMessages->remove($message);
      return $return;
    }
  }
}

class JobNotFoundException extends RecordNotFoundException {
  
}

class AlreadyWorkingException extends AccessDeniedException {
  
}

class InsufficientLevelForJobException extends AccessDeniedException {
  
}

class InsufficientSkillLevelForJobException extends AccessDeniedException {
  
}

class NotWorkingException extends AccessDeniedException {
  
}

class CannotWorkException extends AccessDeniedException {
  
}

class JobNotFinishedException extends AccessDeniedException {
  
}

class JobMessageNotFoundException extends RecordNotFoundException {
  
}
?>