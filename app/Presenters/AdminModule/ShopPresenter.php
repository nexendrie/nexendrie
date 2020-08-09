<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Orm\Shop as ShopEntity;
use Nexendrie\Model\ShopNotFoundException;
use Nexendrie\Forms\AddEditShopFormFactory;
use Nette\Application\UI\Form;

/**
 * Presenter Shop
 *
 * @author Jakub Konečný
 */
final class ShopPresenter extends BasePresenter {
  protected \Nexendrie\Model\Market $model;
  private ShopEntity $shop;
  
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
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionNew(): void {
    $this->requiresPermissions("content", "add");
  }
  
  protected function createComponentAddShopForm(AddEditShopFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Obchod přidán.");
      $this->redirect("Content:shops");
    };
    return $form;
  }
  
  protected function createComponentEditShopForm(AddEditShopFormFactory $factory): Form {
    $form = $factory->create($this->shop);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Změny uloženy.");
      $this->redirect("Content:shops");
    };
    return $form;
  }
}
?>