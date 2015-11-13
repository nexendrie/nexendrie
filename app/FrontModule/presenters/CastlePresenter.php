<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CastleNotFoundException;

/**
 * Presenter Castle
 *
 * @author Jakub Konečný
 */
class CastlePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Castle @autowire */
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
  function renderList() {
    $this->template->castles = $this->model->listOfCastles();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->castle = $this->model->getCastle($id);
    } catch(CastleNotFoundException $e) {
      $this->forward("notfound");
    }
  }
}
?>