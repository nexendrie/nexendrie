<?php
namespace Nexendrie\Presenters;

/**
 * Presenter Market
 *
 * @author Jakub Konečný
 */
class MarketPresenter extends BasePresenter {
  /** @var \Nexendrie\Market */
  protected $model;
  
  /**
   * @param \Nexendrie\Market $model
   */
  function __construct(\Nexendrie\Market $model) {
    $this->model = $model;
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->shops = $this->model->listOfShops();
  }
  
  /**
   * @param int $id Shop's id
   * @return void
   */
  function renderShop($id) {
    try {
      $data = $this->model->showShop($id);
      $this->template->shop = $data["shop"];
      $this->template->items = $data["items"];
    } catch(\Nette\Application\ForbiddenRequestException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param int $id Item's id
   * @return void
   */
  function actionBuy($id) {
    
  }
}
?>