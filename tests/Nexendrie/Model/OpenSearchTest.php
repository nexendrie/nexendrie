<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class OpenSearchTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    protected OpenSearch $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(OpenSearch::class); // @phpstan-ignore assign.propertyType
    }

    public function testCreateDescription(): void
    {
        $result = $this->model->createDescription("Short", "Long", "Description", "tag1 tag2", "users");
        Assert::matchFile(__DIR__ . "/openSearchDescriptionExpected.xml", $result);
    }
}

$test = new OpenSearchTest();
$test->run();
