<?php
namespace Nexendrie\FrontModule\Presenters;

/**
 * Presenter Rss
 *
 * @author Jakub Konečný
 */
class RssPresenter extends BasePresenter {
  /** @var \Nexendrie\Rss */
  protected $model;
  
  /**
   * @param \Nexendrie\Rss $model
   */
  function __construct(\Nexendrie\Rss $model) {
    $this->model = $model;
  }
  
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