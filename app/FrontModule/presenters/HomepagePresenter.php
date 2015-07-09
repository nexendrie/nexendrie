<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Homepage
 *
 * @author Jakub Konečný
 */
class HomepagePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\News */
  protected $model;
  
  function __construct(\Nexendrie\Model\News $model) {
    $this->model = $model;
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $paginator = new \Nette\Utils\Paginator;
    $this->template->news = $this->model->page($paginator);
    $this->template->paginator = $paginator;
  }
}
?>