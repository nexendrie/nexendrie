<?php
namespace Nexendrie\AdminModule\Presenters;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditItemFormFactory,
    Nexendrie\Orm\Item as ItemEntity,
    Nexendrie\Model\ItemNotFoundException;

/**
 * Presenter Item
 *
 * @author Jakub Konečný
 */
class ItemPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Market @autowire */
  protected $model;
  /** @var ItemEntity */
  private $item;
  
  /**
   * @param int $id
   * @return void
   */
  function actionEdit($id) {
    try {
      $this->item = $this->model->getItem($id);
    } catch(ItemNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param AddEditItemFormFactory $factory
   * @return Form
   */
  protected function createComponentEditItemForm(AddEditItemFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->item->dummyArray());
    $form->onSuccess[] = array($this, "editItemFormSucceeded");
    return $form;
  }
  
  function editItemFormSucceeded(Form $form) {
    $this->model->editItem($this->getParameter("id"), $form->getValues(true));
    $this->flashMessage("Změny uloženy.");
  }
}
?>