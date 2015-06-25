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
      if($key == "joined") {
        $day = (int) substr($value, 8, 2);
        $month = (int) substr($value, 5, 2);
        $year = (int) substr($value, 0, 4);
        $value = "$day.$month.$year";
      }
      $this->template->$key = $value;
    }
  }
}
?>