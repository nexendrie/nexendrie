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
  /** @var \Nexendrie\Model\Profile */
  protected $model;
  /** @var \Nexendrie\Model\Castle */
  protected $castleModel;
  /** @var \Nexendrie\Model\Marriage */
  protected $marriageModel;
  /** @var \Nexendrie\Model\Achievements */
  protected $achievementsModel;
  /** @var bool */
  protected $cachingEnabled = false;

  public function __construct(\Nexendrie\Model\Profile $model, \Nexendrie\Model\Castle $castleModel, \Nexendrie\Model\Marriage $marriageModel, \Nexendrie\Model\Achievements $achievementsModel) {
    parent::__construct();
    $this->model = $model;
    $this->castleModel = $castleModel;
    $this->marriageModel = $marriageModel;
    $this->achievementsModel = $achievementsModel;
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
}
?>