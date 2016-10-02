<?php
declare(strict_types=1);

namespace Nexendrie\Model;

/**
 * Rss channel generator
 *
 * @author Jakub Konečný
 */
class Rss {
  /** @var Article */
  protected $articleModel;
  /** @var \Nette\Application\LinkGenerator */
  protected $linkGenerator;
  /** @var Locale */
  protected $localeModel;
  
  use \Nette\SmartObject;
  
  /**
   * @param Article $articleModel
   * @param \Nette\Application\LinkGenerator $linkGenerator
   * @param Locale $localeModel
   */
  function __construct(Article $articleModel, \Nette\Application\LinkGenerator $linkGenerator, Locale $localeModel) {
    $this->articleModel = $articleModel;
    $this->linkGenerator = $linkGenerator;
    $this->localeModel = $localeModel;
  }
  
  /**
   * Generate feed for news
   * 
   * @return \Nexendrie\Responses\RssResponse
   */
  function newsFeed() {
    $items = $this->articleModel->listOfNews();
    $channel = simplexml_load_file(APP_DIR . "/FrontModule/templates/newsFeed.xml");
    unset($channel->channel->link);
    unset($channel->channel->lastBuildDate);
    $channel->channel->addChild("link", $this->linkGenerator->link("Front:Homepage:default"));
    $channel->channel->addChild("lastBuildDate", $this->localeModel->formatDateTime(time()));
    foreach($items as $item) {
      $i = $channel->channel->addChild("item");
      $i->addChild("title", $item->title);
      $link = $this->linkGenerator->link("Front:Article:view", ["id" => $item->id]);
      $i->addChild("link", $link);
      $i->addChild("pubDate", (string) $item->added);
      $i->addChild("description", substr($item->text, 0, 150));
    }
    return new \Nexendrie\Responses\RssResponse($channel);
  }
  
  /**
   * Generate feed for comments
   * 
   * @param int $newsId
   * @return \Nexendrie\Responses\RssResponse
   * @throws ArticleNotFoundException
   */
  function commentsFeed($newsId) {
    try {
      $news = $this->articleModel->view($newsId);
    } catch(ArticleNotFoundException $e) {
      throw $e;
    }
    $comments = $this->articleModel->viewComments($newsId);
    $channel = simplexml_load_file(APP_DIR . "/FrontModule/templates/commentsFeed.xml");
    $old_title = (string) $channel->channel->title;
    unset($channel->channel->link);
    unset($channel->channel->lastBuildDate);
    unset($channel->channel->title);
    $channel->channel->addChild("title", $old_title . $news->title);
    $channel->channel->addChild("link", $this->linkGenerator->link("Front:Article:view", ["id" => $newsId]));
    $channel->channel->addChild("lastBuildDate", $this->localeModel->formatDateTime(time()));
    foreach($comments as $comment) {
      $c = $channel->channel->addChild("item");
      $c->addChild("title", $comment->title);
      $link = $this->linkGenerator->link("Front:Article:view", ["id" => $newsId]);
      $link .= "#comment-$comment->id";
      $c->addChild("link", $link);
      $c->addChild("pubDate", (string) $this->localeModel->formatDateTime($comment->added));
      $c->addChild("description", substr($comment->text, 0, 150));
    }
    return new \Nexendrie\Responses\RssResponse($channel);
  }
}
?>