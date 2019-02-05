<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Rss\RssResponse;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem as Item;
use Nexendrie\Rss\Collection;
use Nette\Application\LinkGenerator;

/**
 * Rss channel generator
 *
 * @author Jakub Konečný
 */
final class Rss {
  /** @var Article */
  protected $articleModel;
  /** @var LinkGenerator */
  protected $linkGenerator;
  /** @var Generator */
  protected $generator;
  
  use \Nette\SmartObject;
  
  public function __construct(Article $articleModel, LinkGenerator $linkGenerator, Generator $generator) {
    $this->articleModel = $articleModel;
    $this->linkGenerator = $linkGenerator;
    $this->generator = $generator;
  }
  
  /**
   * Generate feed for news
   */
  public function newsFeed(): RssResponse {
    $this->generator->title = "Nexendrie - Novinky";
    $this->generator->description = "Novinky v Nexendrii";
    $this->generator->link = $this->linkGenerator->link("Front:Homepage:default");
    $this->generator->dataSource = function() {
      $return = new Collection();
      $items = $this->articleModel->listOfNews();
      /** @var \Nexendrie\Orm\Article $row */
      foreach($items as $row) {
        $link = $this->linkGenerator->link("Front:Article:view", ["id" => $row->id]);
        $return[] = new Item($row->title, $row->text, $link, $row->added);
      }
      return $return;
    };
    return $this->generator->response();
  }
  
  /**
   * Generate feed for comments
   *
   * @throws ArticleNotFoundException
   */
  public function commentsFeed(int $id): RssResponse {
    try {
      $article = $this->articleModel->view($id);
    } catch(ArticleNotFoundException $e) {
      throw $e;
    }
    $this->generator->title = "Nexendrie - Komentáře k " . $article->title;
    $this->generator->description = "Komentáře k článku";
    $this->generator->link = $this->linkGenerator->link("Front:Homepage:default");
    $this->generator->dataSource = function() use($id) {
      $return = new Collection();
      $comments = $this->articleModel->viewComments($id);
      /** @var \Nexendrie\Orm\Comment $comment */
      foreach($comments as $comment) {
        $link = $this->linkGenerator->link("Front:Article:view", ["id" => $id]);
        $link .= "#comment-$comment->id";
        $return[] = new Item($comment->title, $comment->text, $link, $comment->added);
      }
      return $return;
    };
    return $this->generator->response();
  }
}
?>