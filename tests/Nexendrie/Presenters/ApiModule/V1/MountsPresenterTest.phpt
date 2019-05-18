<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

final class MountsPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  protected function checkMounts(\Nette\Application\Responses\JsonResponse $response, int $count): void {
    $json = $response->getPayload();
    Assert::type("array", $json["mounts"]);
    Assert::count($count, $json["mounts"]);
    foreach($json["mounts"] as $mount) {
      Assert::type(\stdClass::class, $mount);
      Assert::type(\stdClass::class, $mount->type);
    }
  }
  
  public function testReadAll() {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $this->checkMounts($response, 12);
    $response = $this->checkJson($action, ["associations" => ["users" => 1]]);
    $this->checkMounts($response, 1);
    $expected = ["message" => "User with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["users" => 50]]);
    $response = $this->checkJson($action, ["associations" => ["mount-types" => 1]]);
    $this->checkMounts($response, 4);
    $expected = ["message" => "Mount type with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["mount-types" => 50]]);
  }
  
  public function testRead() {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["mount"]);
    Assert::type(\stdClass::class, $json["mount"]->type);
    $expected = ["message" => "Mount with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new MountsPresenterTest();
$test->run();
?>