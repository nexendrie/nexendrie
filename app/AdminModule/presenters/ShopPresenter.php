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
  protected $marketModel;
  /** @var ShopEntity */
  private $shop;
  
  /**
   * @param int $id
   */
  function actionEdit($id) {
    try {
      $this->shop = $this->marketModel->get($id);
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
    $form->onSuccess[] = array($this, "addShopFormSucceeded");
    return $form;
  }
  
  function addShopFormSucceeded(Form $form) {
    $this->marketModel->add($form->getValues(true));
    $this->flashMessage("Obchod přidán.");
    $this->redirect("Content:shops");
  }
  
  /**
   * @param AddEditShopFormFactory $factory
   * @return Form
   */
  protected function createComponentEditShopForm(AddEditShopFormFactory $factory) {
    $form = $factory->create();
    $form->setDefaults($this->shop->toArray());
    $form->onSuccess[] = array($this, "editShopFormSucceeded");
    return $form;
  }
  
  /**
   * @param Form $form
   * @return void
   */
  function editShopFormSucceeded(Form $form) {
    $this->marketModel->edit($this->getParameter("id"), $form->getValues(true));
    $this->flashMessage("Změny uloženy.");
  }
}
?>