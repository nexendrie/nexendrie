<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditMealFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Orm\Meal as MealEntity;
use Nexendrie\Model\MealNotFoundException;

/**
 * Presenter Meal
 *
 * @author Jakub Konečný
 */
final class MealPresenter extends BasePresenter {
  protected \Nexendrie\Model\Tavern $model;
  private MealEntity $meal;
  
  public function __construct(\Nexendrie\Model\Tavern $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddMealForm(AddEditMealFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Jídlo přidáno.");
      $this->redirect("Content:meals");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->meal = $this->model->getMeal($id);
    } catch(MealNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditMealForm(AddEditMealFormFactory $factory): Form {
    $form = $factory->create($this->meal);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:meals");
    };
    return $form;
  }
}
?>