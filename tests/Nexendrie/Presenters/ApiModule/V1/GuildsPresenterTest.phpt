<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class GuildsPresenterTest extends \Tester\TestCase {
  use TApiPresenter;
  
  protected function checkGuilds(\Nette\Application\Responses\JsonResponse $response, int $count): void {
    $json = $response->getPayload();
    Assert::type("array", $json["guilds"]);
    Assert::count($count, $json["guilds"]);
    foreach($json["guilds"] as $guild) {
      Assert::type(\stdClass::class, $guild);
      Assert::type(\stdClass::class, $guild->town);
      Assert::type(\stdClass::class, $guild->skill);
      Assert::type("array", $guild->members);
    }
  }
  
  public function testReadAll() {
    $action = $this->getPresenterName() . ":readAll";
    $response = $this->checkJson($action);
    $this->checkGuilds($response, 1);
    $response = $this->checkJson($action, ["associations" => ["towns" => 1]]);
    $this->checkGuilds($response, 0);
    $response = $this->checkJson($action, ["associations" => ["skills" => 1]]);
    $this->checkGuilds($response, 0);
    $expected = ["message" => "Town with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["towns" => 50]]);
    $expected = ["message" => "Skill with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["associations" => ["skills" => 50]]);
  }
  
  public function testRead() {
    $action = $this->getPresenterName() . ":read";
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["guild"]);
    Assert::type(\stdClass::class, $json["guild"]->town);
    Assert::type(\stdClass::class, $json["guild"]->skill);
    Assert::type("array", $json["guild"]->members);
    $expected = ["message" => "Guild with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
  }
}

$test = new GuildsPresenterTest();
$test->run();
?>