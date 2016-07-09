<?php
namespace Nexendrie\Components;

use Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Election as ElectionEntity;

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
    $votes = $this->getVotes(date("Y"), date("n"));
    foreach($votes as $vote) {
      if($vote->voter->id === $this->user->id) return false;
    }
    return true;
  }
  
  /**
   * Get votes from specified month
   * 
   * @param int $year
   * @param int $month
   * @return ElectionEntity[]
   */
  protected function getVotes($year, $month) {
    return $this->orm->elections->findVotedInMonth($this->town->id, $year, $month);
  }
  
  /**
   * Get results of last elections
   * 
   * @return stdClass[]
   */
  protected function getResults() {
    $date = new \DateTime;
    $date->setTimestamp(mktime(0, 0, 0, date("n"), 1, date("Y")));
    $date->modify("-1 month");
    $votes = $this->getVotes($date->format("Y"), $date->format("n"));
    $results = array();
    foreach($votes as $vote) {
      $index = $vote->candidate->username;
      if(isset($results[$index])) {
        $results[$index]->amount++;
      } else {
        $results[$index] = (object) array(
          "candidate" => $vote->candidate, "amount" => 1, "elected" => $vote->elected
        );
      }
    }
    return $results;
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
      case "results":
        $this->template->results = $this->getResults();
        break;
    }
    $this->template->render();
  }
  
  /**
   * @param int $candidate
   * @return void
   */
  function handleVote($candidate) {
    if(!$this->canVote()) {
      $this->presenter->flashMessage("Nemůžeš hlasovat.");
      $this->presenter->redirect(":Front:Homepage:");
    }
    if(!in_array($candidate, $this->getCandidates()->fetchPairs(NULL, "id"))) {
      $this->presenter->flashMessage("Neplatný kandidát.");
      $this->presenter->redirect(":Front:Homepage:");
    }
    $vote = new ElectionEntity;
    $this->orm->elections->attach($vote);
    $vote->town = $this->town;
    $vote->voter = $this->user->id;
    $vote->voter->lastActive = time();
    $vote->candidate = $candidate;
    $this->orm->elections->persistAndFlush($vote);
    $this->presenter->flashMessage("Tvůj hlas byl zaznamenán.");
    $this->presenter->redirect(":Front:Town:elections");
  }
}

interface ElectionsControlFactory {
  /** @return ElectionsControl */
  function create();
}
?>