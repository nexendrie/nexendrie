<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Model\ItemSetNotFoundException,
    Nexendrie\Forms\AddEditItemSetFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Orm\ItemSet as ItemSetEntity,
    Nextras\Orm\Entity\IEntity;

/**
 * Presenter ItemSet
 *
 * @author Jakub Konečný
 */
class ItemSetPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\ItemSet @autowire */
  protected $model;
  /** @var ItemSetEntity */
  private $set;
  
  /**
   * @return void
   */
  function actionAdd(): void {
    $this->requiresPermissions("content", "add");
  }
  
  /**
   * @param AddEditItemSetFormFactory $factory
   * @return Form
   */
  protected function createComponentAddItemSetForm(AddEditItemSetFormFactory $factory): Form {
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
   * @throws \Nette\Application\BadRequestException
   */
  function actionEdit(int $id): void {
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
  protected function createComponentEditItemSetForm(AddEditItemSetFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->set->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->edit($this->getParameter("id"), $values);
      $this->flashMessage("Sada upravena.");
      $this->redirect("Content:itemSets");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionDelete(int $id): void {
    $this->requiresPermissions("content", "delete");
    try {
      $this->model->delete($id);
      $this->flashMessage("Sada smazána.");
      $this->redirect("Content:ItemSets");
    } catch(ItemSetNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>