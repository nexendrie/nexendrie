<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Town as TownEntity;

require __DIR__ . "/../../bootstrap.php";

class TownTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \TUserControl;
  
  /** @var Town */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Town::class);
  }
  
  function testGet() {
    $town = $this->model->get(1);
    Assert::type(TownEntity::class, $town);
  }
  
  function testListOfTowns() {
    $result = $this->model->listOfTowns();
    Assert::type(ICollection::class, $result);
    Assert::type(TownEntity::class, $result->fetch());
  }
  
  function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, TownNotFoundException::class);
  }
  
  function testTownsOnSale() {
    $result = $this->model->townsOnSale();
    Assert::type(ICollection::class, $result);
    /** @var TownEntity $town */
    $town = $result->fetch();
    Assert::type(TownEntity::class, $town);
    Assert::true($town->onMarket);
  }
  
  function testBuy() {
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
  }
  
  function testGetMayor() {
    Assert::null($this->model->getMayor(1));
  }
  
  function testGetTownCitizens() {
    $result = $this->model->getTownCitizens(2);
    Assert::type("array", $result);
    Assert::count(2, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  function testAppointMayor() {
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
  
  function testCanMove() {
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
  
  function testMoveToTown() {
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
  
  function testFound() {
    Assert::exception(function() {
      $this->model->found([]);
    }, AuthenticationNeededException::class);
    $this->login("jakub");
    Assert::exception(function() {
      $this->model->found([]);
    }, InsufficientLevelForFoundTownException::class);
  }
  
  function testGetTownPeasants() {
    $result = $this->model->getTownPeasants(2);
    Assert::type("array", $result);
    Assert::count(1, $result);
    foreach($result as $key => $value) {
      Assert::type("int", $key);
      Assert::type("string", $value);
    }
  }
  
  function testMakeCitizen() {
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

$test = new TownTest;
$test->run();
?>