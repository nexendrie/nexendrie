<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\UserNotFoundException;
use Nette\Application\BadRequestException;

/**
 * Presenter Profile
 *
 * @author Jakub Konečný
 */
final class ProfilePresenter extends BasePresenter {
  protected \Nexendrie\Model\Profile $model;
  protected \Nexendrie\Model\Castle $castleModel;
  protected \Nexendrie\Model\Marriage $marriageModel;
  protected \Nexendrie\Model\Achievements $achievementsModel;
  protected bool $cachingEnabled = false;
  /** @var string[] */
  private array $cacheableActions = ["articles", "skills", "comments", ];

  public function __construct(\Nexendrie\Model\Profile $model, \Nexendrie\Model\Castle $castleModel, \Nexendrie\Model\Marriage $marriageModel, \Nexendrie\Model\Achievements $achievementsModel) {
    parent::__construct();
    $this->model = $model;
    $this->castleModel = $castleModel;
    $this->marriageModel = $marriageModel;
    $this->achievementsModel = $achievementsModel;
  }

  protected function startup(): void {
    parent::startup();
    $this->cachingEnabled = in_array($this->action, $this->cacheableActions, true);
  }

  /**
   * @throws BadRequestException
   */
  public function renderDefault(?string $name = null): void {
    if($name === null) {
      throw new BadRequestException();
    }
    try {
      $user = $this->model->view($name);
      $this->template->profile = $user;
      $this->template->partner = $this->model->getPartner($user->id);
      $this->template->fiance = $this->model->getFiance($user->id);
      $this->template->canProposeMarriage = $this->marriageModel->canPropose($user->id);
      $this->template->ogType = "profile";
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }

  /**
   * @throws BadRequestException
   */
  public function renderArticles(string $name): void {
    try {
      $this->template->articles = $this->model->getArticles($name);
      $this->template->name = $name;
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }

  /**
   * @throws BadRequestException
   */
  public function renderSkills(string $name): void {
    try {
      $this->template->skills = $this->model->getSkills($name);
      $this->template->name = $name;
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }

  /**
   * @throws BadRequestException
   */
  public function renderAchievements(string $name): void {
    try {
      $this->template->userEntity = $this->model->view($name);
      $this->template->name = $name;
      $this->template->achievements = $this->achievementsModel->getAllAchievements();
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }

  /**
   * @throws BadRequestException
   */
  public function renderComments(string $name): void {
    try {
      $this->template->comments = $this->model->getComments($name);
      $this->template->name = $name;
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }

  protected function getDataModifiedTime(): int {
    $time = 0;
    if(isset($this->template->articles)) {
      /** @var \Nexendrie\Orm\Article $article */
      foreach($this->template->articles as $article) {
        $time = max($time, $article->updated);
      }
      return $time;
    }
    if(isset($this->template->skills)) {
      /** @var \Nexendrie\Orm\UserSkill $skill */
      foreach($this->template->skills as $skill) {
        $time = max($time, $skill->updated);
      }
      return $time;
    }
    if(isset($this->template->comments)) {
      /** @var \Nexendrie\Orm\Comment $comment */
      foreach($this->template->comments as $comment) {
        $time = max($time, $comment->created, $comment->article->updated);
      }
      return $time;
    }
    return time();
  }
}
?>