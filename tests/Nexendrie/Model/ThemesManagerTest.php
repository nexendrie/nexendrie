<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";


final class ThemesManagerTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    private ThemesManager $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(ThemesManager::class); // @phpstan-ignore assign.propertyType
    }

    public function testGetList(): void
    {
        $result = $this->model->getList();
        Assert::type("array", $result);
        Assert::count(4, $result);
        Assert::contains("dark-sky", array_keys($result));
        Assert::same("Temná obloha (zavržený)", $result["dark-sky"]);
        Assert::contains("nexendrie", array_keys($result));
        Assert::same("Nexendrie", $result["nexendrie"]);
        Assert::contains("blue-sky", array_keys($result));
        Assert::same("Modrá obloha (zavržený)", $result["blue-sky"]);
        Assert::contains("matrix", array_keys($result));
        Assert::same("Matrix (experimentální)", $result["matrix"]);
    }
}

$test = new ThemesManagerTest();
$test->run();
