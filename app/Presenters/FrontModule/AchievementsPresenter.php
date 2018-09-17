<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * AchievementsPresenter
 *
 * @author Jakub Konečný
 */
final class AchievementsPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Achievements */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  public function __construct(\Nexendrie\Model\Achievements $model, \Nexendrie\Orm\Model $orm) {
    parent::__construct();
    $this->model = $model;
    $this->orm = $orm;
  }
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
  }
  
  public function renderDefault(): void {
    $this->template->achievements = $this->model->getAllAchievements();
    $this->template->userEntity = $this->orm->users->getById($this->user->id);
  }
}
?>