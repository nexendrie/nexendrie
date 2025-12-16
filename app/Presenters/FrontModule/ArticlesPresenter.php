<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Article;
use Nexendrie\Orm\Article as ArticleEntity;

/**
 * Presenter Articles
 *
 * @author Jakub Konečný
 */
final class ArticlesPresenter extends BasePresenter
{
    public function __construct(private readonly Article $model)
    {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $this->template->categories = ArticleEntity::getCategories();
    }

    public function actionCategory(string $category, int $page = 1): void
    {
        if (!array_key_exists($category, ArticleEntity::getCategories())) {
            $this->redirect("Homepage:");
        } elseif ($category === ArticleEntity::CATEGORY_NEWS) {
            $this->redirect("Homepage:", ["page" => $page]);
        } elseif ($category === ArticleEntity::CATEGORY_CHRONICLE) {
            $this->redirect("Chronicle:", ["page" => $page]);
        }
    }

    public function renderCategory(string $category, int $page = 1): void
    {
        $paginator = new \Nette\Utils\Paginator();
        $paginator->page = $page;
        $this->template->category = ArticleEntity::getCategories()[$category];
        $this->template->articles = $this->model->category($category, $paginator);
        $this->template->paginator = $paginator;
    }

    protected function getDataModifiedTime(): int
    {
        if (!isset($this->template->articles)) {
            return 0;
        }
        $time = 0;
        /** @var ArticleEntity $article */
        foreach ($this->template->articles as $article) {
            $time = max($time, $article->updated);
        }
        return $time;
    }
}
