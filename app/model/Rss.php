<?php
namespace Nexendrie;

/**
 * Rss channel generator
 *
 * @author Jakub Konečný
 */
class Rss extends \Nette\Object {
  /** @var \Nexendrie\News */
  protected $newsModel;
  /** @var \Nette\Application\LinkGenerator */
  protected $linkGenerator;
  /** @var \Nexendrie\Locale */
  protected $localeModel;
  
  function __construct(\Nexendrie\News $newsModel, \Nette\Application\LinkGenerator $linkGenerator, \Nexendrie\Locale $localeModel) {
    $this->newsModel = $newsModel;
    $this->linkGenerator = $linkGenerator;
    $this->localeModel = $localeModel;
  }
  
  /**
   * Generate feed for news
   * 
   * @return \Nexendrie\RssResponse
   */
  function newsFeed() {
    $paginator = new \Nette\Utils\Paginator;
    $items = $this->newsModel->page($paginator);
    $channel = simplexml_load_file(APP_DIR . "/templates/newsFeed.xml");
    unset($channel->channel->link);
    unset($channel->channel->lastBuildDate);
    $channel->channel->addChild("link", $this->linkGenerator->link("Homepage:default"));
    $channel->channel->addChild("lastBuildDate", $this->localeModel->formatDateTime(time()));
    foreach($items as $item) {
      $i = $channel->channel->addChild("item");
      $i->addChild("title", $item->title);
      $link = $this->linkGenerator->link("News:view", array("id" => $item->id));
      $i->addChild("link", $link);
      $i->addChild("pubDate", $item->added);
    }
    return new \Nexendrie\RssResponse($channel);
  }
  
  function commentsFeed($news) {
    if(!$this->newsModel->exists($news)) throw new \Nette\ArgumentOutOfRangeException("Specified news does not exist");
    $comments = $this->newsModel->viewComments($news);
    $channel = simplexml_load_file(APP_DIR . "/templates/commentsFeed.xml");
    unset($channel->channel->link);
    unset($channel->channel->lastBuildDate);
    $channel->channel->addChild("link", $this->linkGenerator->link("News:view", array("id" => $news)));
    $channel->channel->addChild("lastBuildDate", $this->localeModel->formatDateTime(time()));
    foreach($comments as $comment) {
      $c = $channel->channel->addChild("item");
      $c->addChild("title", $comment->title);
      $link = $this->linkGenerator->link("News:view", array("id" => $comment->id));
      $c->addChild("link", $link);
      $c->addChild("pubDate", $comment->added);
    }
    return new \Nexendrie\RssResponse($channel);
  }
}
?>