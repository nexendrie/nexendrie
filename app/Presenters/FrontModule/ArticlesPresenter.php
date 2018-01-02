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
  /** @var \Nexendrie\Model\Article */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Article $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function renderDefault(): void {
    $this->template->categories = ArticleEntity::getCategories();
  }
  
  public function actionCategory(string $category, int $page = 1): void {
    if(!array_key_exists($category, ArticleEntity::getCategories())) {
      $this->redirect("Homepage:");
    } elseif($category === ArticleEntity::CATEGORY_NEWS) {
      $this->redirect("Homepage:", ["page" => $page]);
    } elseif($category === ArticleEntity::CATEGORY_CHRONICLE) {
      $this->redirect("Chronicle:", ["page" => $page]);
    }
  }
  
  public function renderCategory(string $category, int $page = 1): void {
    $paginator = new \Nette\Utils\Paginator();
    $paginator->page = $page;
    $this->template->category = ArticleEntity::getCategories()[$category];
    $this->template->articles = $this->model->category($category, $paginator);
    $this->template->paginator = $paginator;
  }
}
?>