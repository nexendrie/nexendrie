<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\IAdventureControlFactory,
    Nexendrie\Components\AdventureControl;

/**
 * Presenter Adventure
 *
 * @author Jakub Konečný
 */
class AdventurePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Adventure @autowire */
  protected $model;
  
  protected function startup(): void {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
    if($this->user->identity->level === 50) {
      $this->flashMessage("Sedláci nemohou podnikat dobrodružství.");
      $this->redirect("Homepage:");
    }
  }
  
  public function actionDefault(): void {
    if(!is_null($this->model->getCurrentAdventure())) {
      return;
    } elseif($this->model->canDoAdventure())  {
      $this->redirect("list");
    }
    $this->flashMessage("Musíš počkat před dalším dobrodružstvím.");
    $this->redirect("Homepage:");
  }
  
  protected function createComponentAdventure(IAdventureControlFactory $factory): AdventureControl {
    return $factory->create();
  }
  
  public function actionMounts(int $id): void {
    if(!is_null($this->model->getCurrentAdventure())) {
      $this->redirect("default");
    }
    $this->template->adventure = $id;
  }
}
?>