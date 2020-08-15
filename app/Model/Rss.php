<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Rss\RssResponse;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssChannelItem as Item;
use Nexendrie\Rss\Collection;
use Nette\Application\LinkGenerator;
use Nextras\Orm\Collection\ICollection;

/**
 * Rss channel generator
 *
 * @author Jakub Konečný
 */
final class Rss {
  protected Article $articleModel;
  protected LinkGenerator $linkGenerator;
  protected Generator $generator;
  protected string $versionSuffix = "";
  
  use \Nette\SmartObject;
  
  public function __construct(Article $articleModel, LinkGenerator $linkGenerator, Generator $generator, SettingsRepository $sr) {
    $this->articleModel = $articleModel;
    $this->linkGenerator = $linkGenerator;
    $this->generator = $generator;
    $this->versionSuffix = $sr->settings["site"]["versionSuffix"];
  }

  protected function versionSuffix(): string {
    if($this->versionSuffix === "") {
      return "";
    }
    return $this->versionSuffix . " ";
  }

  /**
   * Generate feed for news
   */
  public function newsFeed(): RssResponse {
    $versionSuffix = $this->versionSuffix();
    $info = [
      "title" => "Nexendrie $versionSuffix- Novinky", "description" => "Novinky v Nexendrii",
      "link" => $this->linkGenerator->link("Front:Homepage:default"), "language" => "cs",
    ];
    $this->generator->dataSource = function() {
      $return = new Collection();
      $items = $this->articleModel->listOfNews();
      /** @var \Nexendrie\Orm\Article $row */
      foreach($items as $row) {
        $link = $this->linkGenerator->link("Front:Article:view", ["id" => $row->id]);
        $return[] = $item = new Item([
          "title" => $row->title, "description" => $row->text, "link" => $link, "pubDate" => $row->created,
          "comments" => $link . "#comments",
        ]);
      }
      return $return;
    };
    return $this->generator->response($info);
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
    $versionSuffix = $this->versionSuffix();
    $articleLink = $this->linkGenerator->link("Front:Article:view", ["id" => $id]);
    $info = [
      "title" => "Nexendrie $versionSuffix- Komentáře k " . $article->title,
      "description" => "Komentáře k článku " . $article->title, "link" => $articleLink, "language" => "cs",
    ];
    $this->generator->dataSource = function() use($id, $articleLink) {
      $return = new Collection();
      $comments = $this->articleModel->viewComments($id)->orderBy("created", ICollection::DESC);
      /** @var \Nexendrie\Orm\Comment $comment */
      foreach($comments as $comment) {
        $link = $articleLink . "#comment-$comment->id";
        $return[] = new Item([
          "title" => $comment->title, "description" => $comment->text, "link" => $link, "pubDate" => $comment->created,
        ]);
      }
      return $return;
    };
    return $this->generator->response($info);
  }
}
?>