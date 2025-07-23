<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class CastlesPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  public function testReadAll(): void {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $json = $response->getPayload();
    Assert::type("array", $json["castles"]);
    Assert::count(3, $json["castles"]);
    foreach($json["castles"] as $castle) {
      Assert::type(\stdClass::class, $castle);
      Assert::type(\stdClass::class, $castle->owner);
    }
  }
  
  public function testRead(): void {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["castle"]);
    Assert::type(\stdClass::class, $json["castle"]->owner);
    $expected = ["message" => "Castle with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new CastlesPresenterTest();
$test->run();
?>