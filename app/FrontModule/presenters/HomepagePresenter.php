<?php
namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Homepage
 *
 * @author Jakub Konečný
 */
class HomepagePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article @autowire */
  protected $model;
  
  /**
   * @param int $page
   * @return void
   */
  function renderPage($page = 1) {
    $paginator = new \Nette\Utils\Paginator;
    $paginator->page = $page;
    $this->template->news = $this->model->listOfNews($paginator);
    $this->template->paginator = $paginator;
  }
}
?>