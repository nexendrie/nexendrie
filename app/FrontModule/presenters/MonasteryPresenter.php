<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Model\MonasteryNotFoundException,
    Nexendrie\Model\NotInMonasteryException,
    Nexendrie\Model\CannotJoinMonasteryException,
    Nexendrie\Model\CannotPrayException;

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
    $this->template->canJoin = $this->model->canJoin();
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
  
  /**
   * @param int $id
   * @return void
   */
  function actionJoin($id) {
    try {
      $this->model->join($id);
      $this->flashMessage("Vstoupil jsi do kláštera.");
      $this->redirect("default");
    } catch(CannotJoinMonasteryException $e) {
      $this->flashMessage("Nemůžeš vstoupit do kláštera.");
      $this->redirect("Homepage:");
    } catch(MonasteryNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @return void
   */
  function actionPray() {
    try {
      $this->model->pray();
      $this->flashMessage("Modlidba ti přidala 5 životů.");
      $this->redirect("default");
    } catch(CannotPrayException $e) {
      $this->flashMessage("Nemůžeš se modlit (právě teď).");
      $this->redirect("Homepage:");
    }
  }
}
?>