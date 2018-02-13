<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Orm\Shop as ShopEntity,
    Nexendrie\Model\ShopNotFoundException,
    Nexendrie\Forms\AddEditShopFormFactory,
    Nette\Application\UI\Form;

/**
 * Presenter Shop
 *
 * @author Jakub Konečný
 */
class ShopPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Market */
  protected $model;
  /** @var ShopEntity */
  private $shop;
  
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
      $this->shop = $this->model->getShop($id);
    } catch(ShopNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddShopForm(AddEditShopFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->addShop($values);
      $this->flashMessage("Obchod přidán.");
      $this->redirect("Content:shops");
    };
    return $form;
  }
  
  protected function createComponentEditShopForm(AddEditShopFormFactory $factory): Form {
    $form = $factory->create();
    $form->setDefaults($this->shop->toArray());
    $form->onSuccess[] = function(Form $form, array $values) {
      $this->model->editShop((int) $this->getParameter("id"), $values);
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:shops");
    };
    return $form;
  }
}
?>