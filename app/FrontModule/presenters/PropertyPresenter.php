<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Orm\Town as TownEntity,
    Nexendrie\Model\TownNotFoundException;

/**
 * Presenter Assets
 *
 * @author Jakub Konečný
 */
class PropertyPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Property @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Town @autowire */
  protected $townModel;
  /** @var TownEntity */
  private $town;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->requiresLogin();
    $data = $this->model->show();
    $this->template->money = $data["money"];
    $this->template->items = $data["items"];
    $this->template->isLord = $data["isLord"];
    $this->template->towns = $data["towns"];
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionTown($id) {
    $this->requiresLogin();
    try {
      $this->town = $this->townModel->get($id);
    } catch(TownNotFoundException $e) {
      $this->flashMessage("Město nenalezeno.");
      $this->redirect("Homepage:");
    }
    if($this->town->owner->id != $this->user->id) {
      $this->flashMessage("Zadané město ti nepatří.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderTown($id) {
    $this->template->town = $this->town;
  }
}
?>