<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Event;

require __DIR__ . "/../../bootstrap.php";


class EventsTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Events */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Events::class);
  }
  
  function testListOfEvents() {
    $result = $this->model->listOfEvents();
    Assert::type(ICollection::class, $result);
    Assert::type(Event::class, $result->fetch());
  }
  
  function testGetEvent() {
    $event = $this->model->getEvent(1);
    Assert::type(Event::class, $event);
    Assert::exception(function() {
      $this->model->getEvent(50);
    }, EventNotFoundException::class);
  }
  
  function testEditEvent() {
    Assert::exception(function() {
      $this->model->editEvent(50, []);
    }, EventNotFoundException::class);
  }
  
  function testDeleteEvent() {
    Assert::exception(function() {
      $this->model->deleteEvent(50);
    }, EventNotFoundException::class);
    Assert::exception(function() {
      $this->model->deleteEvent(1);
    }, CannotDeleteStartedEventException::class);
  }
  
  function testCalculateAdventuresBonus() {
    $result = $this->model->calculateAdventuresBonus(100);
    Assert::type("int", $result);
  }
  
  function testCalculateWorkBonus() {
    $result = $this->model->calculateWorkBonus(100);
    Assert::type("int", $result);
  }
  
  function testCalculatePrayerLifeBonus() {
    $result = $this->model->calculatePrayerLifeBonus(100);
    Assert::type("int", $result);
  }
  
  function testCalculateTrainingDiscount() {
    $result = $this->model->calculateTrainingDiscount(100);
    Assert::type("int", $result);
  }
  
  function testCalculateShoppingDiscount() {
    $result = $this->model->calculateShoppingDiscount(100);
    Assert::type("int", $result);
  }
  
  function testGetShoppingDiscount() {
    $result = $this->model->getShoppingDiscount();
    Assert::type("int", $result);
  }
  
  function testCalculateRepairingDiscount() {
    $result = $this->model->calculateRepairingDiscount(100);
    Assert::type("int", $result);
  }
}

$test = new EventsTest;
$test->run();
?>