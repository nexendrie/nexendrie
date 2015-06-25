<?php
namespace Nexendrie\Presenters;

/**
 * Presenter Profile
 *
 * @author Jakub Konečný
 */
class ProfilePresenter extends BasePresenter {
  /** @var \Nexendrie\Profile */
  protected $model;
  
  /**
   * @param \Nexendrie\Profile $model
   */
  function __construct(\Nexendrie\Profile $model) {
    $this->model = $model;
  }
  
  /**
   * @param string $username
   * @return void
   */
  function renderDefault($username) {
    if(is_null($username)) $this->forward("notfound");
    $user = $this->model->view($username);
    if(!$user) $this->forward("notfound");
    foreach($user as $key => $value) {
      $this->template->$key = $value;
    }
  }
}
?>