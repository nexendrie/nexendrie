<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Model\MonasteryNotFoundException,
    Nexendrie\Model\NotInMonasteryException;

/**
 * Presenter Monastery
 *
 * @author Jakub Konečný
 */
class MonasteryPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Monastery @autowire */
  protected $model;
  
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
    try {
      $this->template->monastery = $this->model->getByUser();
    } catch(NotInMonasteryException $e) {
      $this->flashMessage("Nejsi v klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->monasteries = $this->model->listOfMonasteries();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->monastery = $this->model->get($id);
    } catch(MonasteryNotFoundException $e) {
      $this->forward("notfound");
    }
  }
}
?>