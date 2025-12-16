<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\SocialIconsControlFactory;
use Nexendrie\Components\SocialIconsControl;
use Nexendrie\Model\Article;

/**
 * Presenter Homepage
 *
 * @author Jakub Konečný
 */
final class HomepagePresenter extends BasePresenter
{
    public function __construct(private readonly Article $model)
    {
        parent::__construct();
    }

    public function renderPage(int $page = 1): void
    {
        $paginator = new \Nette\Utils\Paginator();
        $paginator->page = $page;
        $this->template->articles = $this->model->listOfNews($paginator);
        $this->template->paginator = $paginator;
    }

    public function createComponentSocialIcons(SocialIconsControlFactory $factory): SocialIconsControl
    {
        return $factory->create();
    }

    protected function getDataModifiedTime(): int
    {
        $time = 0;
        /** @var \Nexendrie\Orm\Article $article */
        foreach ($this->template->articles as $article) {
            $time = max($time, $article->updated);
        }
        return $time;
    }
}
