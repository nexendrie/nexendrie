<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Model\TownNotFoundException;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
class TownPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Town @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->town = $this->model->get($this->user->identity->town);
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->towns = $this->model->listOfTowns();
  }
  
  /**
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->town = $this->model->get($id);
    } catch(TownNotFoundException $e) {
      $this->forward("notfound");
    }
  }
}
?>