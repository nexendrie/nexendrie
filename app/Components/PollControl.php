<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nette\Utils\Arrays;
use Nexendrie\Model\PollVotingException;
use Nexendrie\Model\PollNotFoundException;
use Nexendrie\Model\AccessDeniedException;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Poll as PollEntity;
use Nexendrie\Orm\PollVote as PollVoteEntity;

/**
 * Poll Control
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 * @property-write int $id
 */
final class PollControl extends \Nette\Application\UI\Control {
  private PollEntity $poll;
  private int $id;
  
  public function __construct(private readonly \Nette\Security\User $user, private readonly ORM $orm, IUserProfileLinkControlFactory $userProfileLinkControlFactory) {
    $this->addComponent($userProfileLinkControlFactory->create(), "userProfileLink");
  }
  
  /**
   * @throws PollNotFoundException
   */
  public function getPoll(): PollEntity {
    if(isset($this->poll)) {
      return $this->poll;
    }
    $poll = $this->orm->polls->getById($this->id);
    if($poll === null) {
      throw new PollNotFoundException("Specified poll does not exist.");
    }
    $this->poll = $poll;
    return $poll;
  }
  
  /**
   * @throws PollNotFoundException
   */
  protected function setId(int $id): void {
    try {
      $this->id = $id;
      $this->getPoll();
    } catch(PollNotFoundException $e) {
      throw $e;
    }
  }
  
  /**
   * Get votes for the poll
   */
  public function getVotes(): array {
    $return = ["total" => 0, "answers" => []];
    $votes = $this->orm->pollVotes->findByPoll($this->id);
    if($votes->count() > 0) {
      $return["total"] = $votes->count();
      foreach($votes as $vote) {
        $count = (int) Arrays::get($return["answers"], $vote->answer, 0);
        $return["answers"][$vote->answer] = $count + 1;
      }
    }
    return $return;
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/poll.latte");
    $poll = $this->getPoll();
    $this->template->poll = $poll;
    $votes = $this->getVotes();
    $count = count($poll->parsedAnswers);
    for($i = 1; $i <= $count; $i++) {
      if(!isset($votes["answers"][$i])) {
        $votes["answers"][$i] = 0;
      }
    }
    $this->template->votes = $votes;
    $this->template->canVote = $this->canVote();
    $this->template->render();
  }
  
  /**
   * Check whether the user can vote in the poll
   */
  private function canVote(): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif(!$this->user->isAllowed("poll", "vote")) {
      return false;
    }
    $row = $this->orm->pollVotes->getByPollAndUser($this->id, $this->user->id);
    return $row === null;
  }
  
  /**
   * Vote in the poll
   *
   * @throws \Nette\InvalidArgumentException
   * @throws AccessDeniedException
   * @throws PollVotingException
   */
  private function vote(int $answer): void {
    if(!$this->canVote()) {
      throw new AccessDeniedException("You can't vote in this poll.");
    }
    $poll = $this->getPoll();
    if($answer > count($poll->parsedAnswers)) {
      throw new PollVotingException("The poll has less then $answer answers.");
    }
    $vote = new PollVoteEntity();
    $this->orm->pollVotes->attach($vote);
    $vote->poll = $this->poll;
    $vote->user = $this->user->id;
    $vote->answer = $answer;
    $this->orm->pollVotes->persistAndFlush($vote);
  }
  
  public function handleVote(int $answer): void {
    try {
      $this->vote($answer);
      $this->presenter->flashMessage("Hlas uložen.");
    } catch(\Nette\InvalidArgumentException $e) {
      $this->presenter->flashMessage("Zadaná anketa neexistuje.");
    } catch(AccessDeniedException $e) {
      $this->presenter->flashMessage("Nemůžeš hlasovat v této anketě.");
    } catch(PollVotingException $e) {
      $this->presenter->flashMessage("Neplatná volba.");
    }
  }
}
?>