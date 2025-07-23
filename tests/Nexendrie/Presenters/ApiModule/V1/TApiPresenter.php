<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

use Nette\Application\Responses\TextResponse;
use Tester\Assert;
use Nette\Utils\Strings;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\JsonResponse;

/**
 * TApiPresenter
 *
 * @author Jakub Konečný
 */
trait TApiPresenter {
  use \Nexendrie\Presenters\TPresenter;
  
  /**
   * @var string[] METHOD => action
   */
  protected array $forbiddenMethods = [
    "POST" => "create", "PUT" => "update", "PATCH" => "partialUpdate", "DELETE" => "delete",
  ];
  protected bool $readAllRequiresLogin = false;
  
  protected function getPresenterName(): string {
    /** @var string $presenter */
    $presenter = Strings::before(static::class, "PresenterTest");
    return "Api:V1:" . Strings::after($presenter, "\\", -1);
  }
  
  public function testForbiddenActions(): void {
    $presenter = $this->getPresenterName();
    foreach($this->forbiddenMethods as $method => $action) {
      /** @var Application $application */
      $application = $this->getService(Application::class);
      $request = new Request($presenter, $method, ["action" => $action, ]);
      $application->onResponse[0] = function(Application $application, Response $response) use($method) {
        /** @var JsonResponse $response */
        Assert::type(JsonResponse::class, $response);
        Assert::type("array", $response->getPayload());
        $expected = ["message" => "Method $method is not allowed."];
        Assert::same($expected, $response->getPayload());
      };
      ob_start();
      $application->processRequest($request);
      ob_end_clean();
    }
  }
  
  public function testInvalidAssociations(): void {
    $presenter = $this->getPresenterName();
    $expectedNotAllowed = ["message" => "This action is not allowed."];
    if($this->readAllRequiresLogin) {
      $expectedNotLoggedIn = ["message" => "This action requires authentication."];
      $this->checkJsonScheme("$presenter:readAll", $expectedNotLoggedIn, ["associations" => ["abc" => 1]]);
      $this->login();
    }
    $this->checkJsonScheme("$presenter:readAll", $expectedNotAllowed, ["associations" => ["abc" => 1]]);
  }

  public function testOptions(): void {
    $action = $this->getPresenterName() . ":options";
    /** @var TextResponse $response */ // @phpstan-ignore varTag.nativeType
    $response = $this->check($action);
    Assert::type(TextResponse::class, $response);
    Assert::same("", $response->getSource());
  }
}
?>