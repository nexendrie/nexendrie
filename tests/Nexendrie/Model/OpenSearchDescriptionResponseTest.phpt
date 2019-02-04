<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nette\Application\Application;
use Nette\Application\Request;
use Nette\Application\IResponse;

require __DIR__ . "/../../bootstrap.php";

final class OpenSearchDescriptionResponseTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  /** @var OpenSearch */
  protected $model;

  public function setUp() {
    $this->model = $this->getService(OpenSearch::class);
  }

  protected function checkOpenSearchDescription($destination, array $params = [], array $post = []): string {
    $destination = ltrim($destination, ':');
    $pos = strrpos($destination, ':') ?: strlen($destination);
    $presenter = substr($destination, 0, $pos);
    $action = substr($destination, $pos + 1) ?: 'default';
    $params = ["action" => $action] + $params;
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $request = new Request($presenter, "GET", $params, $post);
    $application->onResponse[] = function(Application $application, IResponse $response) {
      /** @var OpenSearchDescriptionResponse $response */
      Assert::type(OpenSearchDescriptionResponse::class, $response);
      Assert::type("string", $response->source);
    };
    ob_start();
    $application->processRequest($request);
    $response = ob_get_clean();
    Assert::type("string", $response);
    $xml = \Tester\DomQuery::fromXml($response);
    Assert::same("OpenSearchDescription", $xml->getName(), "root element is");
    return $response;
  }

  public function testSend() {
    $this->checkOpenSearchDescription("Front:Search:users");
    $this->checkOpenSearchDescription("Front:Search:articlesTitle");
    $this->checkOpenSearchDescription("Front:Search:articlesText");
  }
}

$test = new OpenSearchDescriptionResponseTest();
$test->run();
?>