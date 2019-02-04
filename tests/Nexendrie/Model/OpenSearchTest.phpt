<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class OpenSearchTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var OpenSearch */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(OpenSearch::class);
  }

  public function testCreateDescription() {
    $result = $this->model->createDescription("Short", "Long", "Description", "tag1 tag2", "users");
    Assert::matchFile(__DIR__ . "/openSearchDescriptionExpected.xml", $result);
  }
}

$test = new OpenSearchTest();
$test->run();
?>