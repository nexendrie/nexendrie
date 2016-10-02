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
  
  /**
   * @return void
   */
  function renderNews() {
    $this->sendResponse($this->model->newsFeed());
  }
  
  /**
   * @param int $news Article's id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function renderComments($news) {
    if($news === NULL) throw new \Nette\Application\BadRequestException;
    try {
      $this->sendResponse($this->model->commentsFeed($news));
    } catch(\Nette\Application\BadRequestException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
}
?>