<?php
namespace Nexendrie\Components;

use Nexendrie\Orm\User as UserEntity;

/**
 * ElectionsControl
 *
 * @author Jakub Konečný
 */
class ElectionsControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nexendrie\Orm\Town */
  private $town;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  function setTown(\Nexendrie\Orm\Town $town) {
    $this->town = $town;
  }
  
  /**
   * Get current state of elections
   * 
   * @return string
   */
  protected function getState() {
    $fakeDay = new \DateTime;
    $fakeDay->setDate(date("Y"), date("n"), date("j"));
    if($fakeDay->format("j") <= 7) {
      $state = "results";
    } else {
      $date = new \DateTime;
      $date->setDate(date("Y"), date("n") + 1, 1);
      $date->setTime(0, 0, 0);
      $date->modify("-7 days");
      if($fakeDay->getTimestamp() > $date->getTimestamp()) $state = "voting";
      else $state = "nothing";
    }
    return $state;
  }
  
  /**
   * Get candidates for elections
   * 
   * @return UserEntity[]
   */
  protected function getCandidates() {
    return $this->orm->users->findTownCitizens($this->town->id);
  }
  
  /**
   * Get number of councillors for the town
   * 
   * @return int
   */
  protected function getNumberOfCouncillors() {
    /** @var int */
    $denizens = $this->town->denizens->countStored();
    if($denizens <= 3) return 0;
    elseif($denizens <= 6) return 1;
    else return (int) ($denizens / 5);
  }
  
  /**
   * Check if the user can vote
   * 
   * @return bool
   */
  protected function canVote() {
    if(!$this->user->isAllowed("town", "elect")) return false;
    elseif(!$this->getNumberOfCouncillors()) return false;
    elseif($this->getState() != "voting") return false;
    else return true;
  }
  
  /**
   * @return void
   */
  function render() {
    $this->template->setFile(__DIR__ . "/elections.latte");
    $this->template->state = $this->getState();
    switch($this->template->state) {
      case "voting":
        $this->template->candidates = $this->getCandidates();
        $this->template->councillors = $this->getNumberOfCouncillors();
        $this->template->canVote = $this->canVote();
        break;
    }
    $this->template->render();
  }
  
  /**
   * @param int $candidate
   * @return void
   */
  function handleVote($candidate) {
    
  }
}

interface ElectionsControlFactory {
  /** @return ElectionsControl */
  function create();
}
?>