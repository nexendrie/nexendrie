<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Profile
 *
 * @author Jakub Konečný
 */
class ProfilePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $model;
  
  /**
   * @param string $username
   * @return void
   */
  function renderDefault($username) {
    if(is_null($username)) $this->forward("notfound");
    try {
      $user = $this->model->view($username);
      foreach($user as $key => $value) {
        $this->template->$key = $value;
      }
    } catch(\Nette\Application\BadRequestException $e) {
      $this->forward("notfound");
    }
  }
}
?>