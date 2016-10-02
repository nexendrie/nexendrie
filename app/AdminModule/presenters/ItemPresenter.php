<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nette\Application\UI\Form,
    Nexendrie\Forms\AddEditItemFormFactory,
    Nexendrie\Orm\Item as ItemEntity,
    Nexendrie\Model\ItemNotFoundException,
    Nextras\Orm\Entity\IEntity;

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
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit($id) {
    $this->requiresPermissions("content", "edit");
    try {
      $this->item = $this->model->getItem($id);
    } catch(ItemNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @return void
   */
  function actionAdd() {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditItemFormFactory $factory
   * @return Form
   */
  protected function createComponentAddItemForm(AddEditItemFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->addItem($form->getValues(true));
      $this->flashMessage("Věc přidána.");
      $this->redirect("Content:items");
    };
    return $form;
  }
  
  /**
   * @param AddEditItemFormFactory $factory
   * @return Form
   */
  protected function createComponentEditItemForm(AddEditItemFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->item->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form) {
      $this->model->editItem($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:items");
    };
    return $form;
  }
}
?>