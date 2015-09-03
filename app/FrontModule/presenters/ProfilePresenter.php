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
      $this->template->profile = $this->model->view($username);
    } catch(\Nette\Application\BadRequestException $e) {
      $this->forward("notfound");
    }
  }
}
?>