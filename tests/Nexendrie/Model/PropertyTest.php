<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class PropertyTest extends \Tester\TestCase
{
    use TUserControl;

    protected Property $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(Property::class); // @phpstan-ignore assign.propertyType
    }

    public function testBudget(): void
    {
        Assert::exception(function () {
            $this->model->budget();
        }, AuthenticationNeededException::class);
        $this->login();
        $result = $this->model->budget();
        Assert::type("array", $result);
        Assert::count(2, $result);
    }
}

$test = new PropertyTest();
$test->run();
