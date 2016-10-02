<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Meal;

require __DIR__ . "/../../bootstrap.php";

class TavernTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use \TUserControl;
  
  /** @var Tavern */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Tavern::class);
  }
  
  function testListOfMeals() {
    $result = $this->model->listOfMeals();
    Assert::type(ICollection::class, $result);
    Assert::type(Meal::class, $result->fetch());
  }
  
  function testGetMeal() {
    $meal = $this->model->getMeal(1);
    Assert::type(Meal::class, $meal);
    Assert::exception(function() {
      $this->model->getMeal(50);
    }, MealNotFoundException::class);
  }
  
  function testEditMeal() {
    Assert::exception(function() {
      $this->model->editMeal(50, []);
    }, MealNotFoundException::class);
  }
  
  function testBuyMeal() {
    Assert::exception(function() {
      $this->model->buyMeal(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->buyMeal(50);
    }, MealNotFoundException::class);
  }
}

$test = new TavernTest;
$test->run();
?>