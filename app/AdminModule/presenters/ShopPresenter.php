<?php
namespace Nexendrie\AdminModule\Presenters;

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
  /** @var \Nexendrie\Model\Market @autowire */
  protected $model;
  /** @var ShopEntity */
  private $shop;
  
  /**
   * @param int $id
   */
  function actionEdit($id) {
    try {
      $this->shop = $this->model->getShop($id);
    } catch(ShopNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param AddEditShopFormFactory $factory
   * @return Form
   */
  protected function createComponentAddShopForm(AddEditShopFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->model->addShop($form->getValues(true));
      $this->flashMessage("Obchod přidán.");
      $this->redirect("Content:shops");
    };
    return $form;
  }
  
  /**
   * @param AddEditShopFormFactory $factory
   * @return Form
   */
  protected function createComponentEditShopForm(AddEditShopFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->shop->toArray());
    $form->onSuccess[] = function(Form $form) {
      $this->model->editShop($this->getParameter("id"), $form->getValues(true));
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>