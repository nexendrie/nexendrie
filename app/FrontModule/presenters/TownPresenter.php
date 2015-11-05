<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Model\TownNotFoundException,
    Nexendrie\Model\NotInMonasteryException;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
class TownPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Town @autowire */
  protected $model;
  /** @var \Nexendrie\Model\UserManager @autowire */
  protected $userManager;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->town = $this->model->get($this->user->identity->town);
    $user = $this->userManager->get($this->user->id);
    $this->template->monastery = $user->monastery;
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