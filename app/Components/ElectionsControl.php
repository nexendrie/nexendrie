<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Orm\Election as ElectionEntity,
    Nexendrie\Orm\ElectionResult as ElectionResultEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * ElectionsControl
 *
 * @author Jakub Konečný
 * @property-write \Nexendrie\Orm\Town $town
 */
class ElectionsControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Elections */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nexendrie\Orm\Town */
  private $town;
  
  public function __construct(\Nexendrie\Model\Elections $model, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  public function setTown(\Nexendrie\Orm\Town $town) {
    $this->town = $town;
  }
  
  /**
   * Get current state of elections
   */
  protected function getState(): string {
    $fakeDay = new \DateTime();
    $fakeDay->setDate((int) date("Y"), (int) date("n"), (int) date("j"));
    if($fakeDay->format("j") <= 7) {
      return "results";
    }
    $date = new \DateTime();
    $date->setDate((int) date("Y"), (int) date("n") + 1, 1);
    $date->setTime(0, 0, 0);
    $date->modify("-7 days");
    if($fakeDay->getTimestamp() > $date->getTimestamp()) {
      return "voting";
    }
    return "nothing";
  }
  
  /**
   * Check if the user can vote
   */
  protected function canVote(): bool {
    if(!$this->user->isAllowed("town", "elect")) {
      return false;
    } elseif(!$this->model->getNumberOfCouncillors($this->town->id)) {
      return false;
    } elseif($this->getState() != "voting") {
      return false;
    }
    $votes = $this->getVotes((int) date("Y"), (int) date("n"));
    foreach($votes as $vote) {
      if($vote->voter->id === $this->user->id) {
        return false;
      }
    }
    return true;
  }
  
  /**
   * Get votes from specified month
   *
   * @return ElectionEntity[]|ICollection
   */
  protected function getVotes(int $year, int $month): ICollection {
    return $this->orm->elections->findVotedInMonth($this->town->id, $year, $month);
  }
  
  /**
   * Get results of last elections
   *
   * @return ElectionResultEntity[]|ICollection
   */
  protected function getResults(): ICollection {
    $date = new \DateTime();
    $date->setTimestamp(mktime(0, 0, 0, (int) date("n"), 1, (int) date("Y")));
    //$date->modify("-1 month");
    /*$votes = $this->getVotes($date->format("Y"), $date->format("n"));
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
    return $results;*/
    return $this->orm->electionResults->findByTownAndYearAndMonth($this->town->id, (int) $date->format("Y"), (int) $date->format("n"));
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/elections.latte");
    $this->template->state = $this->getState();
    switch($this->template->state) {
      case "voting":
        $this->template->candidates = $this->model->getCandidates($this->town->id);
        $this->template->councillors = $this->model->getNumberOfCouncillors($this->town->id);
        $this->template->canVote = $this->canVote();
        break;
      case "results":
        $this->template->results = $this->getResults();
        break;
    }
    $this->template->render();
  }
  
  public function handleVote(int $candidate): void {
    if(!$this->canVote()) {
      $this->presenter->flashMessage("Nemůžeš hlasovat.");
      $this->presenter->redirect(":Front:Homepage:");
    }
    if(!in_array($candidate, $this->model->getCandidates($this->town->id)->fetchPairs(NULL, "id"))) {
      $this->presenter->flashMessage("Neplatný kandidát.");
      $this->presenter->redirect(":Front:Homepage:");
    }
    $vote = new ElectionEntity();
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
?>