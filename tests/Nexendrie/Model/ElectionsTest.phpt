<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\User as UserEntity;

require __DIR__ . "/../../bootstrap.php";

final class ElectionsTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Elections */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Elections::class);
  }
  
  public function testGetNumberOfCouncillors() {
    Assert::same(0, $this->model->getNumberOfCouncillors(1));
    Assert::same(1, $this->model->getNumberOfCouncillors(2));
    Assert::same(0, $this->model->getNumberOfCouncillors(5000));
  }
  
  public function testGetCandidates() {
    $result = $this->model->getCandidates(2);
    Assert::type(ICollection::class, $result);
    Assert::type(UserEntity::class, $result->fetch());
  }
}

$test = new ElectionsTest();
$test->run();
?>