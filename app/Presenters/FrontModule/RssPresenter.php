<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

/**
 * Presenter Rss
 *
 * @author Jakub Konečný
 */
class RssPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Rss @autowire */
  protected $model;
  
  public function renderNews() {
    $this->sendResponse($this->model->newsFeed());
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderComments(int $article): void {
    if(is_null($article)) {
      throw new \Nette\Application\BadRequestException;
    }
    try {
      $this->sendResponse($this->model->commentsFeed($article));
    } catch(\Nette\Application\BadRequestException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>