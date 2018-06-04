<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Rss
 *
 * @author Jakub Konečný
 */
final class RssPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Rss */
  protected $model;
  
  public function __construct(\Nexendrie\Model\Rss $model) {
    parent::__construct();
    $this->model = $model;
  }
  
  public function renderNews(): void {
    $this->sendResponse($this->model->newsFeed());
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderComments(int $article = null): void {
    if(is_null($article)) {
      throw new \Nette\Application\BadRequestException;
    }
    try {
      $this->sendResponse($this->model->commentsFeed($article));
    } catch(\Nexendrie\Model\ArticleNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>