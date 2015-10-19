<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Chronicle
 *
 * @author Jakub Konečný
 */
class ChroniclePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article @autowire */
  protected $articleModel;
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->articles = $this->articleModel->listOfChronicle();
  }
}
?>