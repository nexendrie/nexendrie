<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\Elections;
use Nexendrie\Orm\Election as ElectionEntity;
use Nexendrie\Orm\ElectionResult as ElectionResultEntity;
use Nexendrie\Orm\Model as ORM;
use Nextras\Orm\Collection\ICollection;

/**
 * ElectionsControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\DefaultTemplate $template
 * @property-write \Nexendrie\Orm\Town $town
 */
final class ElectionsControl extends \Nette\Application\UI\Control
{
    private \Nexendrie\Orm\Town $town;

    public function __construct(
        private readonly Elections $model,
        private readonly ORM $orm,
        private readonly \Nette\Security\User $user,
        UserProfileLinkControlFactory $userProfileLinkControlFactory
    ) {
        $this->addComponent($userProfileLinkControlFactory->create(), "userProfileLink");
    }

    protected function setTown(\Nexendrie\Orm\Town $town): void
    {
        $this->town = $town;
    }

    /**
     * Get current state of elections
     */
    private function getState(): ElectionsState
    {
        if ((int) date("j") <= 7) {
            return ElectionsState::Results;
        }
        $date = new \DateTime();
        $date->setDate((int) date("Y"), (int) date("n") + 1, 1);
        $date->setTime(0, 0, 0);
        $date->modify("-7 days");
        if (time() > $date->getTimestamp()) {
            return ElectionsState::Voting;
        }
        return ElectionsState::Preparation;
    }

    /**
     * Check if the user can vote
     */
    private function canVote(): bool
    {
        if (!$this->user->isAllowed("town", "elect")) {
            return false;
        } elseif ($this->model->getNumberOfCouncillors($this->town->id) === 0) {
            return false;
        } elseif ($this->getState() !== ElectionsState::Voting) {
            return false;
        }
        $votes = $this->getVotes((int) date("Y"), (int) date("n"));
        return !array_any($votes->fetchAll(), fn (ElectionEntity $vote): bool => $vote->voter->id === $this->user->id);
    }

    /**
     * Get votes from specified month
     *
     * @return ICollection<ElectionEntity>
     */
    private function getVotes(int $year, int $month): ICollection
    {
        return $this->orm->elections->findVotedInMonth($this->town->id, $year, $month);
    }

    /**
     * Get results of last elections
     *
     * @return ICollection<ElectionResultEntity>
     */
    private function getResults(): ICollection
    {
        $date = new \DateTime();
        $date->setTimestamp((int) mktime(0, 0, 0, (int) date("n"), 1, (int) date("Y")));
        $date->modify("-1 month");
        return $this->orm->electionResults->findByTownAndYearAndMonth(
            $this->town->id,
            (int) $date->format("Y"),
            (int) $date->format("n")
        );
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . "/elections.latte");
        $this->template->state = $this->getState();
        switch ($this->template->state) {
            case ElectionsState::Voting:
                $this->template->candidates = $this->model->getCandidates($this->town->id);
                $this->template->councillors = $this->model->getNumberOfCouncillors($this->town->id);
                $this->template->canVote = $this->canVote();
                break;
            case ElectionsState::Results:
                $this->template->results = $this->getResults();
                break;
        }
        $this->template->render();
    }

    public function handleVote(int $candidate): never
    {
        if (!$this->canVote()) {
            $this->presenter->flashMessage("Nemůžeš hlasovat.");
            $this->presenter->redirect(":Front:Homepage:");
        }
        if (!in_array($candidate, $this->model->getCandidates($this->town->id)->fetchPairs(null, "id"), true)) {
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
