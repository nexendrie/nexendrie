<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

use Tester\Assert;
use Nette\Utils\Strings;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\IResponse;
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
  protected $forbiddenMethods = [
    "POST" => "create", "PUT" => "update", "PATCH" => "partialUpdate", "DELETE" => "delete",
  ];
  
  protected function getPresenterName(): string {
    $presenter = Strings::before(static::class, "PresenterTest");
    return "Api:V1:" . Strings::after($presenter, "\\", -1);
  }
  
  public function testForbiddenActions() {
    $presenter = $this->getPresenterName();
    foreach($this->forbiddenMethods as $method => $action) {
      /** @var Application $application */
      $application = $this->getService(Application::class);
      $request = new Request($presenter, $method, ["action" => $action,]);
      $application->onResponse[0] = function(Application $application, IResponse $response) use($method) {
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
  
  public function testInvalidAssociations() {
    $presenter = $this->getPresenterName();
    $expected = ["message" => "This action is not allowed."];
    $this->checkJsonScheme("$presenter:readAll", $expected, ["associations" => ["abc" => 1]]);
  }
}
?>