<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Components\AdventureControlFactory,
    Nexendrie\Components\AdventureControl;

/**
 * Presenter Adventure
 *
 * @author Jakub Konečný
 */
class AdventurePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Adventure @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
    if($this->user->identity->level === 50) {
      $this->flashMessage("Sedláci nemohou podnikat dobrodružství.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function actionDefault() {
    if(!$this->model->getCurrentAdventure()) $this->redirect("list");
  }
  
  /**
   * @param AdventureControlFactory $factory
   * @return AdventureControl
   */
  protected function createComponentAdventure(AdventureControlFactory $factory) {
    return $factory->create();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionMounts($id) {
    if($this->model->getCurrentAdventure()) $this->redirect("default");
    $this->template->adventure = $id;
  }
}
?>