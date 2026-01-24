<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\Rss;

/**
 * Presenter Rss
 *
 * @author Jakub Konečný
 */
final class RssPresenter extends BasePresenter
{
    public function __construct(private readonly Rss $model)
    {
        parent::__construct();
        $this->earlyHints = false;
    }

    public function renderNews(): never
    {
        $this->sendResponse($this->model->newsFeed());
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function renderComments(?int $id = null): never
    {
        if ($id === null) {
            throw new \Nette\Application\BadRequestException();
        }
        try {
            $this->sendResponse($this->model->commentsFeed($id));
        } catch (\Nexendrie\Model\ArticleNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }
}
