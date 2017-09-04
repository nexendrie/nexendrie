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
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->item = $this->model->getItem($id);
    } catch(ItemNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddItemForm(AddEditItemFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->addItem($values);
      $this->flashMessage("Věc přidána.");
      $this->redirect("Content:items");
    };
    return $form;
  }
  
  protected function createComponentEditItemForm(AddEditItemFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->item->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editItem($this->getParameter("id"), $values);
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:items");
    };
    return $form;
  }
}
?>