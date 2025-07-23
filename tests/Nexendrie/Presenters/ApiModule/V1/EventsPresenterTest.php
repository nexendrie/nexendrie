<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class EventsPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  public function testReadAll(): void {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $json = $response->getPayload();
    Assert::type("array", $json["events"]);
    Assert::count(1, $json["events"]);
    foreach($json["events"] as $event) {
      Assert::type(\stdClass::class, $event);
      Assert::type("array", $event->adventures);
    }
  }
  
  public function testRead(): void {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["event"]);
    Assert::type("array", $json["event"]->adventures);
    $expected = ["message" => "Event with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new EventsPresenterTest();
$test->run();
?>