<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Description of NewsPresenter
 *
 * @author Jakub Konečný
 */
class NewsPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\News @autowire */
  protected $model;
  
  /**
   * @return void
   */
  function renderPage($page) {
    if($page == 1) $this->redirect("Homepage:");
    $paginator = new \Nette\Utils\Paginator;
    $this->template->news = $this->model->page($paginator, $page);
    $this->template->paginator = $paginator;
  }
}
?>