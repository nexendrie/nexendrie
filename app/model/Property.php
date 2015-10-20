<?php
namespace Nexendrie\Model;

/**
 * Assets Model
 *
 * @author Jakub Konečný
 */
class Property extends \Nette\Object {
  /** @var \Nexendrie\Model\Job*/
  protected $jobModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Job $jobModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->jobModel = $jobModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Show user's possessions
   * 
   * @return array
   * @throws AuthenticationNeededException
   */
  function show() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $return = array();
    $user = $this->orm->users->getById($this->user->id);
    $return["money"] = $user->moneyT;
    $return["items"] = $user->items;
    $return["isLord"] = ($user->group->level >= 350);
    $return["towns"] = $user->ownedTowns;
    return $return;
  }
  
  /**
   * Show user's budget
   * 
   * @return array
   * @throws AuthenticationNeededException
   */
  function budget() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $budget = array(
      "incomes" => array(
        "work" => 0,
        "adventures" => 0,
        "taxes" => 0
      ),
      "expenses" => array(
        "incomeTax" => 0
    ));
    $jobs = $this->orm->userJobs->findFromMonth($this->user->id);
    foreach($jobs as $job) {
      if($job->finished) $budget["incomes"]["work"] += $job->earned + $job->extra;
      else $budget["incomes"]["work"] += array_sum($this->jobModel->calculateReward($job));
    }
    return $budget;
  }
}
?>