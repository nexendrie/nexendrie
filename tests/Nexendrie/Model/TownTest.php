<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Town as TownEntity;

require __DIR__ . "/../../bootstrap.php";

final class TownTest extends \Tester\TestCase {
  use TUserControl;

  protected Town $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Town::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testGet(): void {
    $town = $this->model->get(1);
    Assert::type(TownEntity::class, $town);
  }
  
  public function testListOfTowns(): void {
    $result = $this->model->listOfTowns();
    Assert::type(ICollection::class, $result);
    Assert::type(TownEntity::class, $result->fetch());
  }
  
  public function testEdit(): void {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, TownNotFoundException::class);
    $town = $this->model->get(1);
    $name = $town->name;
    $this->model->edit($town->id, ["name" => "abc"]);
    Assert::same("abc", $town->name);
    $this->model->edit($town->id, ["name" => $name]);
  }
  
  public function testTownsOnSale(): void {
    $result = $this->model->townsOnSale();
    Assert::type(ICollection::class, $result);
    /** @var TownEntity $town */
    $town = $result->fetch();
    Assert::type(TownEntity::class, $town);
    Assert::true($town->onMarket);
  }
  
  public function testBuy(): void {
    Assert::exception(function() {
      $this->model->buy(1);
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->buy(50);
    }, TownNotFoundException::class);
    Assert::exception(function() {
      $this->model->buy(1);
    }, TownNotOnSaleException::class);
    Assert::exception(function() {
      $this->model->buy(5);
    }, CannotBuyTownException::class);
    $this->login("Vladěna");
    Assert::exception(function() {
      $this->model->buy(5);
    }, CannotBuyOwnTownException::class);
    $this->login();
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->buy(5);
      });
    }, InsufficientFundsException::class);
  }
  
  public function testGetMayor(): void {
    Assert::null($this->model->getMayor(1));
  }
  
  public function testGetTownCitizens(): void {
    $result = $this->model->getTownCitizens(2);
    Assert::type("array", $result);
    Assert::count(2, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  public function testAppointMayor(): void {
    Assert::exception(function() {
      $this->model->appointMayor(1, 1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->appointMayor(50, 1);
    }, TownNotFoundException::class);
    Assert::exception(function() {
      $this->model->appointMayor(2, 50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->appointMayor(1, 50);
    }, TownNotOwnedException::class);
    Assert::exception(function() {
      $this->model->appointMayor(2, 4);
    }, UserDoesNotLiveInTheTownException::class);
    Assert::exception(function() {
      $this->model->appointMayor(2, 1);
    }, InsufficientLevelForMayorException::class);
  }
  
  public function testCanMove(): void {
    Assert::false($this->model->canMove());
    $this->login("Rahym");
    Assert::false($this->model->canMove());
    $this->login("kazimira");
    Assert::false($this->model->canMove());
    $this->login();
    Assert::type("bool", $this->model->canMove());
    $this->login("Jakub");
    Assert::type("bool", $this->model->canMove());
  }
  
  public function testMoveToTown(): void {
    Assert::exception(function() {
      $this->model->moveToTown(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->moveToTown(50);
    }, TownNotFoundException::class);
    Assert::exception(function() {
      $this->model->moveToTown(2);
    }, CannotMoveToSameTownException::class);
    $this->login("Rahym");
    Assert::exception(function() {
      $this->model->moveToTown(2);
    }, CannotMoveToTownException::class);
  }
  
  public function testFound(): void {
    Assert::exception(function() {
      $this->model->found([]);
    }, AuthenticationNeededException::class);
    $this->login("Jakub");
    Assert::exception(function() {
      $this->model->found([]);
    }, InsufficientLevelForFoundTownException::class);
    $this->login();
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->found([]);
      });
    }, InsufficientFundsException::class);
  }
  
  public function testGetTownPeasants(): void {
    $result = $this->model->getTownPeasants(2);
    Assert::type("array", $result);
    Assert::count(1, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  public function testMakeCitizen(): void {
    Assert::exception(function() {
      $this->model->makeCitizen(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->makeCitizen(50);
    }, UserNotFoundException::class);
    Assert::exception(function() {
      $this->model->makeCitizen(0);
    }, UserDoesNotLiveInTheTownException::class);
    Assert::exception(function() {
      $this->model->makeCitizen(3);
    }, TooHighLevelException::class);
  }

  public function testCanManage(): void {
    Assert::false($this->model->canManage($this->model->get(1)));
    $this->login();
    Assert::true($this->model->canManage($this->model->get(1)));
    Assert::true($this->model->canManage($this->model->get(2)));
    Assert::false($this->model->canManage($this->model->get(10)));
  }
}

$test = new TownTest();
$test->run();
?>