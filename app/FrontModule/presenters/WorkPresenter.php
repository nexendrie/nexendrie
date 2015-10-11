<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Work
 *
 * @author Jakub Konečný
 */
class WorkPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Job @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionStart($id) {
    
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionFinish($id) {
    
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionWork() {
    
  }
}
?>