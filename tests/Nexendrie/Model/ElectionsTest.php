<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\User as UserEntity;

require __DIR__ . "/../../bootstrap.php";

final class ElectionsTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    protected Elections $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(Elections::class); // @phpstan-ignore assign.propertyType
    }

    public function testGetNumberOfCouncillors(): void
    {
        Assert::same(0, $this->model->getNumberOfCouncillors(1));
        Assert::same(1, $this->model->getNumberOfCouncillors(2));
        Assert::same(0, $this->model->getNumberOfCouncillors(5000));
    }

    public function testGetCandidates(): void
    {
        $result = $this->model->getCandidates(2);
        Assert::type(ICollection::class, $result);
        Assert::type(UserEntity::class, $result->fetch());
    }
}

$test = new ElectionsTest();
$test->run();
