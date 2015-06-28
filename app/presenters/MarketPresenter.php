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
    
  }
}
?>