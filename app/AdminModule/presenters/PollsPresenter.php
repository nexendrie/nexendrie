<?php
namespace Nexendrie\AdminModule\Presenters;

/**
 * Presenter Polls
 *
 * @author Jakub Konečný
 */
class PollsPresenter extends BasePresenter {
  /** @var \Nexendrie\Polls */
  protected $model;
  
  /**
   * @param \Nexendrie\Polls $model
   */
  function __construct(\Nexendrie\Polls $model) {
    $this->model = $model;
  }
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresPermissions("poll", "add");
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->polls = $this->model->all();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    
  }
}
?>