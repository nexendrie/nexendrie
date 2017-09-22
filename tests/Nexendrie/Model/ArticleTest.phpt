<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Article as ArticleEntity,
    Nexendrie\Orm\Comment as CommentEntity;

require __DIR__ . "/../../bootstrap.php";

final class ArticleTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Article */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Article::class);
  }
  
  public function testListOfArticles() {
    $result = $this->model->listOfArticles();
    Assert::type(ICollection::class, $result);
    Assert::type(ArticleEntity::class, $result->fetch());
  }
  
  public function testListOfNews() {
    $result = $this->model->listOfNews();
    Assert::type(ICollection::class, $result);
    Assert::type(ArticleEntity::class, $result->fetch());
  }
  
  public function testCategory() {
    $result = $this->model->category(ArticleEntity::CATEGORY_CHRONICLE);
    Assert::type(ICollection::class, $result);
    Assert::type(ArticleEntity::class, $result->fetch());
  }
  
  public function testView() {
    $article = $this->model->view(1);
    Assert::type(ArticleEntity::class, $article);
    Assert::exception(function() {
      $this->model->view(50);
    }, ArticleNotFoundException::class);
  }
  
  public function testViewComments() {
    $result1 = $this->model->viewComments();
    Assert::type(ICollection::class, $result1);
    Assert::type(CommentEntity::class, $result1->fetch());
    Assert::count(5, $result1);
    $result2 = $this->model->viewComments(1);
    Assert::type(ICollection::class, $result2);
    Assert::type(CommentEntity::class, $result1->fetch());
    Assert::count(1, $result2);
  }
  
  public function testAddArticle() {
    Assert::exception(function() {
      $this->model->addArticle([]);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->addArticle([]);
    }, MissingPermissionsException::class);
  }
  
  public function testEditArticle() {
    Assert::exception(function() {
      $this->model->editArticle(1, []);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->editArticle(50, []);
    }, ArticleNotFoundException::class);
    Assert::exception(function() {
      $this->model->editArticle(1, []);
    }, MissingPermissionsException::class);
    $this->login();
    $article = $this->model->view(1);
    $title = $article->title;
    $this->model->editArticle($article->id, ["title" => "abc"]);
    Assert::same("abc", $article->title);
    $this->model->editArticle($article->id, ["title" => $title]);
  }
  
  public function testExists() {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
}

$test = new ArticleTest();
$test->run();
?>