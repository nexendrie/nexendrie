<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Homepage
 *
 * @author Jakub Konečný
 */
final class HomepagePresenter extends BasePresenter {
  protected \Nexendrie\Model\Article $model;
  
  public function __construct(\Nexendrie\Model\Article $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function renderPage(int $page = 1): void {
    $paginator = new \Nette\Utils\Paginator();
    $paginator->page = $page;
    $this->template->articles = $this->model->listOfNews($paginator);
    $this->template->paginator = $paginator;
  }

  protected function getDataModifiedTime(): int {
    $time = 0;
    /** @var \Nexendrie\Orm\Article $article */
    foreach($this->template->articles as $article) {
      $time = max($time, $article->updated);
    }
    return $time;
  }
}
?>