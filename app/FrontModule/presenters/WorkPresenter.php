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
  function actionDefault() {
    if(!$this->model->isWorking()) $this->redirect("offers");
  }
  
  /**
   * @return void
   */
  function actionOffers() {
    if($this->model->isWorking()) {
      $this->flashMessage("Už pracuješ.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function renderOffers() {
    $this->template->offers = $this->model->findAvailableJobs();
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