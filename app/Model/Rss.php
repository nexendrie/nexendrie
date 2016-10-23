<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Rss\RssResponse,
    Nexendrie\Rss\Generator,
    Nexendrie\Rss\RssChannelItem;

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
   * @return RssResponse
   */
  function newsFeed(): RssResponse {
    $generator = new Generator;
    $generator->title = "Nexendrie - Novinky";
    $generator->description = "Novinky v Nexendrii";
    $generator->link = $this->linkGenerator->link("Front:Homepage:default");
    $generator->dateTimeFormat = $this->localeModel->formats["dateTimeFormat"];
    $items = $this->articleModel->listOfNews();
    $generator->dataSource = function() use($items) {
      $return = [];
      /** @var \Nexendrie\Orm\Article $row */
      foreach($items as $row) {
        $item = new RssChannelItem($row->title, $row->text, $this->linkGenerator->link("Front:Article:view", ["id" => $row->id]), $row->addedAt);
        $return[] = $item;
      }
      return $return;
    };
    return new RssResponse($generator->generate());
  }
  
  /**
   * Generate feed for comments
   * 
   * @param int $newsId
   * @return RssResponse
   * @throws ArticleNotFoundException
   */
  function commentsFeed(int $newsId): RssResponse {
    try {
      $article = $this->articleModel->view($newsId);
    } catch(ArticleNotFoundException $e) {
      throw $e;
    }
    $generator = new Generator;
    $generator->title = "Nexendrie - Komentáře k " . $article->title;
    $generator->description = "Komentáře k článku";
    $generator->link = $this->linkGenerator->link("Front:Homepage:default");
    $generator->dateTimeFormat = $this->localeModel->formats["dateTimeFormat"];
    $comments = $this->articleModel->viewComments($newsId);
    $generator->dataSource = function() use($comments, $newsId) {
      $return = [];
      /** @var \Nexendrie\Orm\Comment $row */
      foreach($comments as $row) {
        $link = $this->linkGenerator->link("Front:Article:view", ["id" => $newsId]);
        $link .= "#comment-$row->id";
        $item = new RssChannelItem($row->title, $row->text, $link, $row->addedAt);
        $return[] = $item;
      }
      return $return;
    };
    return new RssResponse($generator->generate());
  }
}
?>