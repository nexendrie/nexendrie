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
  /** @var \Nexendrie\Model\ItemSet */
  protected $model;
  /** @var ItemSetEntity */
  private $set;
  
  public function __construct(\Nexendrie\Model\ItemSet $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
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
   * @throws \Nette\Application\BadRequestException
   */
  public function actionEdit(int $id): void {
    $this->requiresPermissions("content", "edit");
    try {
      $this->set = $this->model->get($id);
    } catch(ItemSetNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  protected function createComponentEditItemSetForm(AddEditItemSetFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->set->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->edit((int) $this->getParameter("id"), $values);
      $this->flashMessage("Sada upravena.");
      $this->redirect("Content:itemSets");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionDelete(int $id): void {
    $this->requiresPermissions("content", "delete");
    try {
      $this->model->delete($id);
      $this->flashMessage("Sada smazána.");
      $this->redirect("Content:ItemSets");
    } catch(ItemSetNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
}
?>