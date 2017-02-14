<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\AddEditMealFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\Meal as MealEntity,
    Nexendrie\Model\MealNotFoundException;

/**
 * Presenter Meal
 *
 * @author Jakub Konečný
 */
class MealPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Tavern @autowire */
  protected $model;
  /** @var MealEntity */
  private $meal;
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditMealFormFactory $factory
   * @return Form
   */
  protected function createComponentAddMealForm(AddEditMealFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->addMeal($values);
      $this->flashMessage("Jídlo přidáno.");
      $this->redirect("Content:meals");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->meal = $this->model->getMeal($id);
    } catch(MealNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditMealFormFactory $factory
   * @return Form
   */
  protected function createComponentEditMealForm(AddEditMealFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->meal->toArray());
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editMeal($this->getParameter("id"), $values);
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:meals");
    };
    return $form;
  }
}
?>