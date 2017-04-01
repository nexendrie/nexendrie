<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

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
  protected function startup() {
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
  function actionDefault(): void {
    if($this->model->getCurrentAdventure()) {
      
    } elseif($this->model->canDoAdventure())  {
      $this->redirect("list");
    } else {
      $this->flashMessage("Musíš počkat před dalším dobrodružstvím.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param AdventureControlFactory $factory
   * @return AdventureControl
   */
  protected function createComponentAdventure(AdventureControlFactory $factory): AdventureControl {
    return $factory->create();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionMounts(int $id): void {
    if($this->model->getCurrentAdventure()) {
      $this->redirect("default");
    }
    $this->template->adventure = $id;
  }
}
?>