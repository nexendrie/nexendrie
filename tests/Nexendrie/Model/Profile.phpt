<?php
namespace Nexendrie\Model;

use Tester\Assert,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Group;

require __DIR__ . "/../../bootstrap.php";

class ProfileTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  /** @var Profile */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Profile::class);
  }
  
  function testView() {
    $user = $this->model->view("system");
    Assert::type(UserEntity::class, $user);
    Assert::exception(function() {
      $this->model->view("abc");
    }, UserNotFoundException::class);
  }
  
  function testGetListOfLords() {
    $result = $this->model->getListOfLords();
    Assert::type("array", $result);
    Assert::count(4, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  function testUserLife() {
    $result = $this->model->userLife(1);
    Assert::type("array", $result);
    Assert::count(2, $result);
    Assert::same(90, $result[0]);
    Assert::same(90, $result[1]);
  }
  
  function testGetPath() {
    Assert::same(Group::PATH_TOWER, $this->model->getPath(1));
    Assert::same(Group::PATH_CHURCH, $this->model->getPath(2));
    Assert::same(Group::PATH_CITY, $this->model->getPath(3));
    Assert::exception(function() {
      $this->model->getPath(50);
    }, UserNotFoundException::class);
  }
  
  function testCountCompletedAdventures() {
    Assert::type("int", $this->model->countCompletedAdventures(1));
  }
  
  function testCountProducedBeers() {
    $result = $this->model->countProducedBeers(3);
    Assert::type("int", $result);
    Assert::true($result > 0);
  }
  
  function testCountPunishments() {
    Assert::same(0, $this->model->countPunishments(1));
    Assert::same(1, $this->model->countPunishments(2));
  }
  
  function testCountLessons() {
    $result = $this->model->countLessons(1);
    Assert::type("int", $result);
    Assert::true($result > 0);
  }
  
  function testCountMessages() {
    $result = $this->model->countMessages(1);
    Assert::type("array", $result);
    Assert::count(2, $result);
    Assert::same(9, $result["sent"]);
    Assert::same(2, $result["recieved"]);
  }
  
  function testGetPartner() {
    $partner1 = $this->model->getPartner(4);
    Assert::type(UserEntity::class, $partner1);
    Assert::same(1, $partner1->id);
    $partner2 = $this->model->getPartner(1);
    Assert::type(UserEntity::class, $partner2);
    Assert::same(4, $partner2->id);
    Assert::null($this->model->getPartner(2));
  }
  
  function testGetFiance() {
    $partner1 = $this->model->getFiance(3);
    Assert::type(UserEntity::class, $partner1);
    Assert::same(6, $partner1->id);
    $partner2 = $this->model->getFiance(6);
    Assert::type(UserEntity::class, $partner2);
    Assert::same(3, $partner2->id);
    Assert::null($this->model->getFiance(2));
  }
}

$test = new ProfileTest;
$test->run();
?>