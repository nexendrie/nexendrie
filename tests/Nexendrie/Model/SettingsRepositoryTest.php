<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";


final class SettingsRepositoryTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    protected SettingsRepository $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(SettingsRepository::class); // @phpstan-ignore assign.propertyType
    }

    public function testGetSettings(): void
    {
        $result = $this->model->settings;
        Assert::type("array", $result);
    }
}

$test = new SettingsRepositoryTest();
$test->run();
