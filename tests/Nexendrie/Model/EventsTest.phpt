<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Event;

require __DIR__ . "/../../bootstrap.php";

final class EventsTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Events */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(Events::class);
  }
  
  public function testListOfEvents() {
    $result = $this->model->listOfEvents();
    Assert::type(ICollection::class, $result);
    Assert::type(Event::class, $result->fetch());
  }
  
  public function testGetEvent() {
    Assert::type(Event::class, $this->model->getEvent(1));
    Assert::exception(function() {
      $this->model->getEvent(50);
    }, EventNotFoundException::class);
  }
  
  public function testEditEvent() {
    Assert::exception(function() {
      $this->model->editEvent(50, []);
    }, EventNotFoundException::class);
    $event = $this->model->getEvent(1);
    $this->model->editEvent(1, ["start" => $event->startAt, "name" => "abc"]);
    Assert::same("abc", $event->name);
    $this->model->editEvent(1, ["name" => $event->name]);
  }
  
  public function testDeleteEvent() {
    Assert::exception(function() {
      $this->model->deleteEvent(50);
    }, EventNotFoundException::class);
    Assert::exception(function() {
      $this->model->deleteEvent(1);
    }, CannotDeleteStartedEventException::class);
  }
  
  public function testGetCurrentEvents() {
    $events = $this->model->getCurrentEvents();
    Assert::type("array", $events);
  }
  
  public function testCalculateAdventuresBonus() {
    $result = $this->model->calculateAdventuresBonus(100);
    Assert::type("int", $result);
  }
  
  public function testCalculateWorkBonus() {
    $result = $this->model->calculateWorkBonus(100);
    Assert::type("int", $result);
  }
  
  public function testCalculatePrayerLifeBonus() {
    $result = $this->model->calculatePrayerLifeBonus(100);
    Assert::type("int", $result);
  }
  
  public function testCalculateTrainingDiscount() {
    $result = $this->model->calculateTrainingDiscount(100);
    Assert::type("int", $result);
  }
  
  public function testCalculateShoppingDiscount() {
    $result = $this->model->calculateShoppingDiscount(100);
    Assert::type("int", $result);
  }
  
  public function testGetShoppingDiscount() {
    $result = $this->model->getShoppingDiscount();
    Assert::type("int", $result);
  }
  
  public function testCalculateRepairingDiscount() {
    $result = $this->model->calculateRepairingDiscount(100);
    Assert::type("int", $result);
  }
}

$test = new EventsTest();
$test->run();
?>