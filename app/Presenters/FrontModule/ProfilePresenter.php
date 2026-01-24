<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Achievements;
use Nexendrie\Model\Job;
use Nexendrie\Model\Marriage;
use Nexendrie\Model\NotWorkingException;
use Nexendrie\Model\Profile;
use Nexendrie\Model\UserNotFoundException;
use Nette\Application\BadRequestException;

/**
 * Presenter Profile
 *
 * @author Jakub Konečný
 */
final class ProfilePresenter extends BasePresenter
{
    /** @var string[] */
    private array $cacheableActions = ["articles", "skills", "comments",];

    public function __construct(
        private readonly Profile $model,
        private readonly Marriage $marriageModel,
        private readonly Achievements $achievementsModel,
        private readonly Job $jobModel
    ) {
        parent::__construct();
        $this->cachingEnabled = false;
    }

    protected function startup(): void
    {
        parent::startup();
        $this->cachingEnabled = in_array($this->action, $this->cacheableActions, true);
    }

    /**
     * @throws BadRequestException
     */
    public function renderDefault(?string $name = null): void
    {
        if ($name === null) {
            throw new BadRequestException();
        }
        try {
            $user = $this->model->view($name);
            $this->template->profile = $user;
            $this->template->partner = $this->model->getPartner($user->id);
            $this->template->fiance = $this->model->getFiance($user->id);
            try {
                $job = $this->jobModel->getCurrentJob($user->id);
            } catch (NotWorkingException) {
                $job = null;
            }
            $this->template->job = $job;
            $this->template->canProposeMarriage = $this->marriageModel->canPropose($user->id);
            $this->template->ogType = "profile";
        } catch (UserNotFoundException) {
            throw new BadRequestException();
        }
    }

    /**
     * @throws BadRequestException
     */
    public function renderArticles(string $name): void
    {
        try {
            $this->template->articles = $this->model->getArticles($name);
            $this->template->name = $name;
        } catch (UserNotFoundException) {
            throw new BadRequestException();
        }
    }

    /**
     * @throws BadRequestException
     */
    public function renderSkills(string $name): void
    {
        try {
            $this->template->skills = $this->model->getSkills($name);
            $this->template->name = $name;
        } catch (UserNotFoundException) {
            throw new BadRequestException();
        }
    }

    /**
     * @throws BadRequestException
     */
    public function renderAchievements(string $name): void
    {
        try {
            $this->template->userEntity = $this->model->view($name);
            $this->template->name = $name;
            $this->template->achievements = $this->achievementsModel->achievements;
        } catch (UserNotFoundException) {
            throw new BadRequestException();
        }
    }

    /**
     * @throws BadRequestException
     */
    public function renderComments(string $name): void
    {
        try {
            $this->template->comments = $this->model->getComments($name);
            $this->template->name = $name;
        } catch (UserNotFoundException) {
            throw new BadRequestException();
        }
    }

    protected function getDataModifiedTime(): int
    {
        $time = 0;
        if (isset($this->template->articles)) {
            /** @var \Nexendrie\Orm\Article $article */
            foreach ($this->template->articles as $article) {
                $time = max($time, $article->updated);
            }
            return $time;
        }
        if (isset($this->template->skills)) {
            /** @var \Nexendrie\Orm\UserSkill $skill */
            foreach ($this->template->skills as $skill) {
                $time = max($time, $skill->updated);
            }
            return $time;
        }
        if (isset($this->template->comments)) {
            /** @var \Nexendrie\Orm\Comment $comment */
            foreach ($this->template->comments as $comment) {
                $time = max($time, $comment->created, $comment->article->updated);
            }
            return $time;
        }
        return time();
    }
}
