<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\UserNotFoundException,
    Nette\Application\BadRequestException;

/**
 * Presenter Profile
 *
 * @author Jakub Konečný
 */
class ProfilePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Castle @autowire */
  protected $castleModel;
  /** @var \Nexendrie\Model\Marriage @autowire */
  protected $marriageModel;
  
  /**
   * @throws BadRequestException
   */
  public function renderDefault(?string $username = NULL): void {
    if(is_null($username)) {
      throw new BadRequestException();
    }
    try {
      $user = $this->model->view($username);
      $this->template->profile = $user;
      $this->template->castle = $this->castleModel->getUserCastle($user->id);
      $this->template->partner = $this->model->getPartner($user->id);
      $this->template->fiance = $this->model->getFiance($user->id);
      $this->template->canProposeMarriage = $this->marriageModel->canPropose($user->id);
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }
  
  public function renderArticles(string $username): void {
    try {
      $this->template->articles = $this->model->getArticles($username);
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }
  
  public function renderSkills(string $username): void {
    try {
      $this->template->skills = $this->model->getSkills($username);
    } catch(UserNotFoundException $e) {
      throw new BadRequestException();
    }
  }
}
?>