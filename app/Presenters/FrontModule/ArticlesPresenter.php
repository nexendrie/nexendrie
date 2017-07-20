<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Orm\Article as ArticleEntity;

/**
 * Presenter Articles
 *
 * @author Jakub Konečný
 */
class ArticlesPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Article @autowire */
  protected $model;
  
  function renderDefault(): void {
    $this->template->categories = ArticleEntity::getCategories();
  }
  
  function actionCategory(string $category, int $page = 1): void {
    if(!array_key_exists($category, ArticleEntity::getCategories())) {
      $this->redirect("Homepage:");
    } elseif($category === ArticleEntity::CATEGORY_NEWS) {
      $this->redirect("Homepage:", ["page" => $this->getParameter("page")]);
    } elseif($category === ArticleEntity::CATEGORY_CHRONICLE) {
      $this->redirect("Chronicle:", ["page" => $this->getParameter("page")]);
    }
  }
  
  function renderCategory(string $category, int $page = 1): void {
    $paginator = new \Nette\Utils\Paginator;
    $paginator->page = $page;
    $this->template->category = ArticleEntity::getCategories()[$category];
    $this->template->articles = $this->model->category($category, $paginator);
    $this->template->paginator = $paginator;
  }
}
?>