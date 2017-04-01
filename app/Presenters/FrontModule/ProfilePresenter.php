<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\UserNotFoundException;

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
   * @param string $username
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function renderDefault($username): void {
    if(is_null($username)) {
      throw new \Nette\Application\BadRequestException;
    }
    try {
      $user = $this->model->view($username);
      $this->template->profile = $user;
      $this->template->castle = $this->castleModel->getUserCastle($user->id);
      $this->template->partner = $this->model->getPartner($user->id);
      $this->template->fiance = $this->model->getFiance($user->id);
      $this->template->canProposeMarriage = $this->marriageModel->canPropose($user->id);
    } catch(UserNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>