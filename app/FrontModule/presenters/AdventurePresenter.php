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
    $this->redirect("list");
  }
  
  /**
   * @param AdventureControlFactory $factory
   * @return AdventureControl
   */
  protected function createComponentAdventure(AdventureControlFactory $factory) {
    return $factory->create();
  }
}
?>