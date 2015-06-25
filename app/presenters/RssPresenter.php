<?php
namespace Nexendrie\Presenters;

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
}
?>