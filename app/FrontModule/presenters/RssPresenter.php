<?php
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
   * @param int $news News' id
   * @return void
   */
  function renderComments($news) {
    if($news === NULL) $this->forward("News:notfound");
    try {
      $this->sendResponse($this->model->commentsFeed($news));
    } catch(\Nette\Application\BadRequestException $e) {
      $this->forward("News:notfound");
    }
  }
}
?>