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
  /** @var \Nexendrie\Model\Mount @autowire */
  protected $mountModel;
  /** @var \Nexendrie\Model\Skills @autowire */
  protected $skillsModel;
  /** @var \Nexendrie\Model\Pub @autowire */
  protected $pubModel;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresPermissions("content", "list");
  }
  
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
  
  /**
   * @return void
   */
  function renderTowns() {
    $this->template->towns = $this->townModel->listOfTowns();
  }
  
  /**
   * @return void
   */
  function renderMounts() {
    $this->template->mounts = $this->mountModel->listOfMounts();
  }
  
  /**
   * @return void
   */
  function renderSkills() {
    $this->template->skills = $this->skillsModel->listOfSkills();
  }
  
  /**
   * @return void
   */
  function renderMeals() {
    $this->template->meals = $this->pubModel->listOfMeals();
  }
}
?>