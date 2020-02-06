<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nette\Application\UI\Form;
use Nexendrie\Forms\AddEditItemFormFactory;
use Nexendrie\Orm\Item as ItemEntity;
use Nexendrie\Model\ItemNotFoundException;

/**
 * Presenter Item
 *
 * @author Jakub Konečný
 */
final class ItemPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Market */
  protected $model;
  /** @var ItemEntity */
  private $item;
  
  public function __construct(\Nexendrie\Model\Market $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->item = $this->model->getItem($id);
    } catch(ItemNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddItemForm(AddEditItemFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Věc přidána.");
      $this->redirect("Content:items");
    };
    return $form;
  }
  
  protected function createComponentEditItemForm(AddEditItemFormFactory $factory): Form {
    $form = $factory->create($this->item);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:items");
    };
    return $form;
  }
}
?>