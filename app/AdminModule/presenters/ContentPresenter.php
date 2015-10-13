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
  /** @var \Nexendrie\Model\Job @autowire */
  protected $jobModel;
  /** @var \Nexendrie\Model\Town @autowire */
  protected $townModel;
  
  /**
   * @return void
   */
  function renderShops() {
    $this->template->shops = $this->marketModel->listOfShops();
  }
  
  /**
   * @return void
   */
  function renderItems() {
    $this->template->items = $this->marketModel->listOfItems();
  }
  
  /**
   * @return void
   */
  function renderJobs() {
    $this->template->jobs = $this->jobModel->listOfJobs();
  }
  
  function renderTowns() {
    $this->template->towns = $this->townModel->listOfTowns();
  }
}
?>