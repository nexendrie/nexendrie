<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Homepage
 *
 * @author Jakub Konečný
 */
final class HomepagePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Article $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function renderPage(int $page = 1): void {
    $paginator = new \Nette\Utils\Paginator();
    $paginator->page = $page;
    $this->template->news = $this->model->listOfNews($paginator);
    $this->template->paginator = $paginator;
  }
}
?>