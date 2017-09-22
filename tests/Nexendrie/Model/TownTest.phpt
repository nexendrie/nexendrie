<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Town as TownEntity;

require __DIR__ . "/../../bootstrap.php";

final class TownTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Town */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Town::class);
  }
  
  public function testGet() {
    $town = $this->model->get(1);
    Assert::type(TownEntity::class, $town);
  }
  
  public function testListOfTowns() {
    $result = $this->model->listOfTowns();
    Assert::type(ICollection::class, $result);
    Assert::type(TownEntity::class, $result->fetch());
  }
  
  public function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, TownNotFoundException::class);
    $town = $this->model->get(1);
    $name = $town->name;
    $this->model->edit($town->id, ["name" => "abc"]);
    Assert::same("abc", $town->name);
    $this->model->edit($town->id, ["name" => $name]);
  }
  
  public function testTownsOnSale() {
    $result = $this->model->townsOnSale();
    Assert::type(ICollection::class, $result);
    /** @var TownEntity $town */
    $town = $result->fetch();
    Assert::type(TownEntity::class, $town);
    Assert::true($town->onMarket);
  }
  
  public function testBuy() {
    Assert::exception(function() {
      $this->model->buy(1);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->buy(50);
    }, TownNotFoundException::class);
    Assert::exception(function() {
      $this->model->buy(1);
    }, TownNotOnSaleException::class);
    Assert::exception(function() {
      $this->model->buy(5);
    }, InsufficientLevelForTownException::class);
    $this->login("system");
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
  
  public function testGetMayor() {
    Assert::null($this->model->getMayor(1));
  }
  
  public function testGetTownCitizens() {
    $result = $this->model->getTownCitizens(2);
    Assert::type("array", $result);
    Assert::count(2, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  public function testAppointMayor() {
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
  
  public function testCanMove() {
    Assert::false($this->model->canMove());
    $this->login("Rahym");
    Assert::false($this->model->canMove());
    $this->login("kazimira");
    Assert::false($this->model->canMove());
    $this->login();
    Assert::type("bool", $this->model->canMove());
    $this->login("jakub");
    Assert::type("bool", $this->model->canMove());
  }
  
  public function testMoveToTown() {
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
  
  public function testFound() {
    Assert::exception(function() {
      $this->model->found([]);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
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
  
  public function testGetTownPeasants() {
    $result = $this->model->getTownPeasants(2);
    Assert::type("array", $result);
    Assert::count(1, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  public function testMakeCitizen() {
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
}

$test = new TownTest();
$test->run();
?>