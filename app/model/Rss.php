<?php
namespace Nexendrie\Model;

/**
 * Rss channel generator
 *
 * @author Jakub Konečný
 */
class Rss extends \Nette\Object {
  /** @var \Nexendrie\Model\News */
  protected $newsModel;
  /** @var \Nette\Application\LinkGenerator */
  protected $linkGenerator;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  /**
   * @param \Nexendrie\Model\News $newsModel
   * @param \Nette\Application\LinkGenerator $linkGenerator
   * @param \Nexendrie\Model\Locale $localeModel
   */
  function __construct(News $newsModel, \Nette\Application\LinkGenerator $linkGenerator, Locale $localeModel) {
    $this->newsModel = $newsModel;
    $this->linkGenerator = $linkGenerator;
    $this->localeModel = $localeModel;
  }
  
  /**
   * Generate feed for news
   * 
   * @return \Nexendrie\Responses\RssResponse
   */
  function newsFeed() {
    $paginator = new \Nette\Utils\Paginator;
    $items = $this->newsModel->page($paginator);
    $channel = simplexml_load_file(APP_DIR . "/FrontModule/templates/newsFeed.xml");
    unset($channel->channel->link);
    unset($channel->channel->lastBuildDate);
    $channel->channel->addChild("link", $this->linkGenerator->link("Front:Homepage:default"));
    $channel->channel->addChild("lastBuildDate", $this->localeModel->formatDateTime(time()));
    foreach($items as $item) {
      $i = $channel->channel->addChild("item");
      $i->addChild("title", $item->title);
      $link = $this->linkGenerator->link("Front:News:view", array("id" => $item->id));
      $i->addChild("link", $link);
      $i->addChild("pubDate", $item->added);
      $i->addChild("description", substr($item->text, 0 , 150));
    }
    return new \Nexendrie\Responses\RssResponse($channel);
  }
  
  /**
   * Generate feed for comments
   * 
   * @param int $newsId
   * @return \Nexendrie\Responses\RssResponse
   * @throws NewsNotFoundException
   */
  function commentsFeed($newsId) {
    try {
      $news = $this->newsModel->view($newsId);
    } catch(NewsNotFoundException $e) {
      throw $e;
    }
    $comments = $this->newsModel->viewComments($newsId);
    $channel = simplexml_load_file(APP_DIR . "/FrontModule/templates/commentsFeed.xml");
    $old_title = (string) $channel->channel->title;
    unset($channel->channel->link);
    unset($channel->channel->lastBuildDate);
    unset($channel->channel->title);
    $channel->channel->addChild("title", $old_title . $news->title);
    $channel->channel->addChild("link", $this->linkGenerator->link("Front:News:view", array("id" => $newsId)));
    $channel->channel->addChild("lastBuildDate", $this->localeModel->formatDateTime(time()));
    foreach($comments as $comment) {
      $c = $channel->channel->addChild("item");
      $c->addChild("title", $comment->title);
      $link = $this->linkGenerator->link("Front:News:view", array("id" => $newsId));
      $link .= "#comment-$comment->id";
      $c->addChild("link", $link);
      $c->addChild("pubDate", $this->localeModel->formatDateTime($comment->added));
      $c->addChild("description", substr($comment->text, 0 , 150));
    }
    return new \Nexendrie\Responses\RssResponse($channel);
  }
}
?>