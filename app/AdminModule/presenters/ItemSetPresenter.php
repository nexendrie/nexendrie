<?php
namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\ItemSetNotFoundException,
    Nexendrie\Forms\AddEditItemSetFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\ItemSet as ItemSetEntity;

/**
 * Presenter ItemSet
 *
 * @author Jakub Konečný
 */
class ItemSetPresenter extends BasePresenter {
  /** @var Nexendrie\Model\ItemSet @autowire */
  protected $model;
  /** @var ItemSetEntity */
  private $set;
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditItemSetFormFactory $factory
   * @return Form
   */
  protected function createComponentAddItemSetForm(AddEditItemSetFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->add($values);
      $this->flashMessage("Sada přidána.");
      $this->redirect("Content:itemSets");
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
      $this->set = $this->model->get($id);
    } catch(ItemSetNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param AddEditItemSetFormFactory $factory
   * @return Form
   */
  protected function createComponentEditItemSetForm(AddEditItemSetFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->set->dummyArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->edit($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Sada upravena.");
      $this->redirect("Content:itemSets");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionDelete($id) {
    $this->requiresPermissions("content", "delete");
    try {
      $this->set = $this->model->delete($id);
      $this->flashMessage("Sada smazána.");
      $this->redirect("Content:ItemSets");
    } catch(ItemSetNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>