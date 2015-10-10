<?php
namespace Nexendrie\AdminModule\Presenters;

/**
 * Presenter Content
 *
 * @author Jakub Konečný
 */
class ContentPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Market @autowire */
  protected $marketModel;
  
  function renderShops() {
    $this->template->shops = $this->marketModel->listOfShops();
  }
}
?>