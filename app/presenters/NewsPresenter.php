<?php
namespace Nexendrie\Presenters;

/**
 * Description of NewsPresenter
 *
 * @author Jakub Konečný
 */
class NewsPresenter extends BasePresenter {
  /** @var \Nexendrie\News*/
  protected $model;
  
  /**
   * @return void
   */
  function startup() {
    parent::startup();
    $this->model = $this->context->getService("model.news");
  }
  
  function renderPage($page) {
    if($page == 1) $this->redirect("Homepage:");
    $paginator = new \Nette\Utils\Paginator;
    $this->template->news = $this->model->page($paginator, $page);
    $this->template->paginator = $paginator;
  }
  
  function renderView($id) {
    $new = $this->model->view($id);
    if(!$new) $this->forward("notfound");
    $this->template->new = $new;
  }
}
?>