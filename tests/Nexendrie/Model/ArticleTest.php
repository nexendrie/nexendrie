<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Article as ArticleEntity;
use Nexendrie\Orm\Comment as CommentEntity;

require __DIR__ . "/../../bootstrap.php";

final class ArticleTest extends \Tester\TestCase {
  use TUserControl;

  protected Article $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Article::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testListOfArticles(): void {
    $result = $this->model->listOfArticles();
    Assert::type(ICollection::class, $result);
    Assert::type(ArticleEntity::class, $result->fetch());
  }
  
  public function testListOfNews(): void {
    $result = $this->model->listOfNews();
    Assert::type(ICollection::class, $result);
    Assert::type(ArticleEntity::class, $result->fetch());
  }
  
  public function testCategory(): void {
    $result = $this->model->category(ArticleEntity::CATEGORY_CHRONICLE);
    Assert::type(ICollection::class, $result);
    Assert::type(ArticleEntity::class, $result->fetch());
  }
  
  public function testView(): void {
    $article = $this->model->view(1);
    Assert::type(ArticleEntity::class, $article);
    Assert::exception(function() {
      $this->model->view(50);
    }, ArticleNotFoundException::class);
  }
  
  public function testViewComments(): void {
    $result1 = $this->model->viewComments();
    Assert::type(ICollection::class, $result1);
    Assert::type(CommentEntity::class, $result1->fetch());
    Assert::count(5, $result1);
    $result2 = $this->model->viewComments(1);
    Assert::type(ICollection::class, $result2);
    Assert::type(CommentEntity::class, $result1->fetch());
    Assert::count(1, $result2);
  }
  
  public function testAddArticle(): void {
    Assert::exception(function() {
      $this->model->addArticle([]);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->addArticle([]);
    }, MissingPermissionsException::class);
    $this->login();
    $data = [
      "title" => "ABCDE", "text" => "abcde", "category" => ArticleEntity::CATEGORY_CHRONICLE
    ];
    $id = $this->model->addArticle($data);
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    /** @var ArticleEntity $article */
    $article = $orm->articles->getById($id);
    Assert::type(ArticleEntity::class, $article);
    $orm->articles->removeAndFlush($article);
  }
  
  public function testEditArticle(): void {
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
  
  public function testExists(): void {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
}

$test = new ArticleTest();
$test->run();
?>