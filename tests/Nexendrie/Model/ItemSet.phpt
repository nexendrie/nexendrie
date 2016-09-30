<?php
namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\ItemSet as ItemSetEntity;

require __DIR__ . "/../../bootstrap.php";


class ItemSetTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var ItemSet */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(ItemSet::class);
  }
  
  function testListOfSets() {
    $result = $this->model->listOfSets();
    Assert::type(ICollection::class, $result);
    Assert::type(ItemSetEntity::class, $result->fetch());
  }
  
  function testGet() {
    $set = $this->model->get(1);
    Assert::type(ItemSetEntity::class, $set);
    Assert::exception(function() {
      $this->model->get(50);
    }, ItemSetNotFoundException::class);
  }
  
  function testEdit() {
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, ItemSetNotFoundException::class);
  }
  
  function testDelete() {
    Assert::exception(function() {
      $this->model->delete(50);
    }, ItemSetNotFoundException::class);
  }
}

$test = new ItemSetTest;
$test->run();
?>