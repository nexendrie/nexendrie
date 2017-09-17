<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Relationships\OneHasMany,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Group;

require __DIR__ . "/../../bootstrap.php";

final class ProfileTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Profile */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Profile::class);
  }
  
  public function testView() {
    $user = $this->model->view("system");
    Assert::type(UserEntity::class, $user);
    Assert::exception(function() {
      $this->model->view("abc");
    }, UserNotFoundException::class);
  }
  
  public function testGetListOfLords() {
    $result = $this->model->getListOfLords();
    Assert::type("array", $result);
    Assert::count(4, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  public function testGetPath() {
    Assert::same(Group::PATH_TOWER, $this->model->getPath(1));
    Assert::same(Group::PATH_CHURCH, $this->model->getPath(2));
    Assert::same(Group::PATH_CITY, $this->model->getPath(3));
    Assert::exception(function() {
      $this->model->getPath(50);
    }, UserNotFoundException::class);
  }
  
  public function testGetPartner() {
    $partner1 = $this->model->getPartner(4);
    Assert::type(UserEntity::class, $partner1);
    Assert::same(1, $partner1->id);
    $partner2 = $this->model->getPartner(1);
    Assert::type(UserEntity::class, $partner2);
    Assert::same(4, $partner2->id);
    Assert::null($this->model->getPartner(2));
  }
  
  public function testGetFiance() {
    $partner1 = $this->model->getFiance(3);
    Assert::type(UserEntity::class, $partner1);
    Assert::same(6, $partner1->id);
    $partner2 = $this->model->getFiance(6);
    Assert::type(UserEntity::class, $partner2);
    Assert::same(3, $partner2->id);
    Assert::null($this->model->getFiance(2));
  }
  
  public function testGetArticles() {
    $articles = $this->model->getArticles("admin");
    Assert::type(OneHasMany::class, $articles);
    Assert::count(12, $articles);
    Assert::exception(function() {
      $this->model->getArticles("abc");
    }, UserNotFoundException::class);
  }
  
  public function testGetSkills() {
    $skills = $this->model->getSkills("Rahym");
    Assert::type(OneHasMany::class, $skills);
    Assert::count(2, $skills);
    Assert::exception(function() {
      $this->model->getSkills("abc");
    }, UserNotFoundException::class);
  }
}

$test = new ProfileTest;
$test->run();
?>