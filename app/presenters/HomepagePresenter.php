<?php
namespace Nexendrie\Presenters;

/**
 * Presenter Homepage
 *
 * @author Jakub Konečný
 */
class HomepagePresenter extends BasePresenter {
  /**
   * @return void
   */
  function renderDefault() {
    $model = $this->context->getService("model.news");
    $paginator = new \Nette\Utils\Paginator;
    $this->template->news = $model->page($paginator);
    $this->template->paginator = $paginator;
  }
}
?>