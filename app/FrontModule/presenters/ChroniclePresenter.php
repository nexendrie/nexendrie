<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Chronicle
 *
 * @author Jakub Konečný
 */
class ChroniclePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Chronicle @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->articles = $this->model->articles();
  }
}
?>