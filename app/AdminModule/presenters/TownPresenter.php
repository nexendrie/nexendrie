<?php
namespace Nexendrie\AdminModule\Presenters;

use Nexendrie\Forms\AddEditTownFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\Town as TownEntity,
    Nexendrie\Model\TownNotFoundException;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
class TownPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Town @autowire */
  protected $model;
  /** @var TownEntity */
  private $town;
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditTownFormFactory $factory
   * @return Form
   */
  protected function createComponentAddTownForm(AddEditTownFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $values = $form->getValues(true);
      if($values["owner"] === 0) $values["onMarket"] = true;
      $this->model->add($values);
      $this->flashMessage("Město přidáno.");
      $this->redirect("Content:towns");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->town = $this->model->get($id);
    } catch(TownNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param AddEditTownFormFactory $factory
   * @return Form
   */
  protected function createComponentEditTownForm(AddEditTownFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->town->dummyArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->edit($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Město upraveno.");
      $this->redirect("Content:towns");
    };
    return $form;
  }
}
?>