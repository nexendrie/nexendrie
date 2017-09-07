<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\ItemSet as ItemSetEntity;

require __DIR__ . "/../../bootstrap.php";

final class ItemSetTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var ItemSet */
  protected $model;
  
  public function setUp() {
    $this->model = $this->getService(ItemSet::class);
  }
  
  public function testListOfSets() {
    $result = $this->model->listOfSets();
    Assert::type(ICollection::class, $result);
    Assert::type(ItemSetEntity::class, $result->fetch());
  }
  
  public function testGet() {
    $set = $this->model->get(1);
    Assert::type(ItemSetEntity::class, $set);
    Assert::exception(function() {
      $this->model->get(50);
    }, ItemSetNotFoundException::class);
  }
  
  public function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, ItemSetNotFoundException::class);
  }
  
  public function testDelete() {
    Assert::exception(function() {
      $this->model->delete(50);
    }, ItemSetNotFoundException::class);
  }
}

$test = new ItemSetTest;
$test->run();
?>