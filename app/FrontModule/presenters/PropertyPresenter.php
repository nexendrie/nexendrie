<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Assets
 *
 * @author Jakub Konečný
 */
class PropertyPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Property @autowire */
  protected $model;
  
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
}
?>