<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\ItemSet as ItemSetEntity;

require __DIR__ . "/../../bootstrap.php";

final class ItemSetTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var ItemSet */
  protected $model;
  
  protected function setUp() {
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
    $set = $this->model->get(1);
    $name = $set->name;
    $this->model->edit(1, ["name" => "abc"]);
    Assert::same("abc", $set->name);
    $this->model->edit(1, ["name" => $name]);
  }
  
  public function testDelete() {
    Assert::exception(function() {
      $this->model->delete(50);
    }, ItemSetNotFoundException::class);
  }
}

$test = new ItemSetTest();
$test->run();
?>