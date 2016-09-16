<?php
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
  
  /**
   * @return void
   */
  function renderDefault() {
    $this->template->categories = ArticleEntity::getCategories();
  }
  
  /**
   * @param string $category
   * @param int $page
   * @return void
   */
  function actionCategory($category, $page = 1) {
    if(!array_key_exists($category, ArticleEntity::getCategories())) {
      $this->redirect("Homepage:");
    } elseif($category === ArticleEntity::CATEGORY_NEWS) {
      $this->redirect("Homepage:", ["page" => $this->getParameter("page")]);
    } elseif($category === ArticleEntity::CATEGORY_CHRONICLE) {
      $this->redirect("Chronicle:", ["page" => $this->getParameter("page")]);
    }
  }
  
  /**
   * @param string $category
   * @param int $page
   * @return void
   */
  function renderCategory($category, $page = 1) {
    $paginator = new \Nette\Utils\Paginator;
    $paginator->page = $page;
    $this->template->category = ArticleEntity::getCategories()[$category];
    $this->template->articles = $this->model->category($category, $paginator);
    $this->template->paginator = $paginator;
  }
}
?>