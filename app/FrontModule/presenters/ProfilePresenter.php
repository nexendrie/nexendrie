<?php
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
  
  /**
   * @param string $username
   * @return void
   */
  function renderDefault($username) {
    if(is_null($username)) $this->forward("notfound");
    try {
      $user = $this->model->view($username);
      $this->template->profile = $user;
      $this->template->castle = $this->castleModel->getUserCastle($user->id);
    } catch(UserNotFoundException $e) {
      $this->forward("notfound");
    }
  }
}
?>