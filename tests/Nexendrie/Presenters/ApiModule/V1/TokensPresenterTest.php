<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

require __DIR__ . "/../../../../bootstrap.php";

use Tester\Assert;

/**
 * @skip
 */
final class TokensPresenterTest extends \Tester\TestCase {
  use TApiPresenter;

  public function __construct() {
    $this->forbiddenMethods = ["PUT" => "update", "PATCH" => "partialUpdate",];
    $this->readAllRequiresLogin = true;
  }

  private function checkTokens(\Nette\Application\Responses\JsonResponse $response, int $count): void {
    $json = $response->getPayload();
    Assert::type("array", $json["tokens"]);
    Assert::count($count, $json["tokens"]);
    foreach($json["tokens"] as $token) {
      Assert::type(\stdClass::class, $token);
      Assert::type("int", $token->id);
      Assert::type("string", $token->token);
      Assert::type("string", $token->created);
      Assert::type("string", $token->expire);
    }
  }
  
  public function testReadAll(): void {
    $action = $this->getPresenterName() . ":readAll";
    $expected = ["message" => "This action requires authentication."];
    $this->checkJsonScheme($action, $expected);
    $this->login();
    $response = $this->checkJson($action);
    $this->checkTokens($response, 2);
    $this->login("Rahym");
    $response = $this->checkJson($action);
    $this->checkTokens($response, 1);
  }
  
  public function testRead(): void {
    $action = $this->getPresenterName() . ":read";
    $expected = ["message" => "This action requires authentication."];
    $this->checkJsonScheme($action, $expected, ["id" => 1]);
    $this->login();
    $response = $this->checkJson($action, ["id" => 1]);
    $json = $response->getPayload();
    Assert::type(\stdClass::class, $json["token"]);
    Assert::same("test1", $json["token"]->token);
    $this->login();
    $expected = ["message" => "Token with id 50 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 50]);
    $this->login("Rahym");
    $expected = ["message" => "Token with id 1 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 1]);
  }

  public function testCreate(): void {
    $action = $this->getPresenterName() . ":create";
    $expected = ["message" => "E-mail not found."];
    $this->checkJsonScheme($action, $expected);
    $_SERVER["PHP_AUTH_USER"] = "jakub.konecny2@centrum.cz";
    $expected = ["message" => "Invalid password."];
    $this->checkJsonScheme($action, $expected);
  }

  public function testDelete(): void {
    $action = $this->getPresenterName() . ":delete";
    $expected = ["message" => "This action requires authentication."];
    $this->checkJsonScheme($action, $expected, ["id" => 2]);
    $this->login();
    $expected = ["message" => "Token with id 2 was deleted."];
    $this->checkJsonScheme($action, $expected, ["id" => 2]);
    $this->login("Rahym");
    $expected = ["message" => "Token with id 1 was not found."];
    $this->checkJsonScheme($action, $expected, ["id" => 1]);
  }
}

$test = new TokensPresenterTest();
$test->run();
?>