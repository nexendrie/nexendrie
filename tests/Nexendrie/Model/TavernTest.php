<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Meal;

require __DIR__ . "/../../bootstrap.php";

final class TavernTest extends \Tester\TestCase {
  use TUserControl;

  protected Tavern $model;
  
  protected function setUp(): void {
    $this->model = $this->getService(Tavern::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testListOfMeals(): void {
    $result = $this->model->listOfMeals();
    Assert::type(ICollection::class, $result);
    Assert::type(Meal::class, $result->fetch());
  }
  
  public function testGetMeal(): void {
    $meal = $this->model->getMeal(1);
    Assert::type(Meal::class, $meal);
    Assert::exception(function() {
      $this->model->getMeal(50);
    }, MealNotFoundException::class);
  }
  
  public function testEditMeal(): void {
    Assert::exception(function() {
      $this->model->editMeal(50, []);
    }, MealNotFoundException::class);
    $meal = $this->model->getMeal(1);
    $name = $meal->name;
    $this->model->editMeal($meal->id, ["name" => "abc"]);
    Assert::same("abc", $meal->name);
    $this->model->editMeal($meal->id, ["name" => $name]);
  }
  
  public function testBuyMeal(): void {
    Assert::exception(function() {
      $this->model->buyMeal(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->buyMeal(50);
    }, MealNotFoundException::class);
    Assert::exception(function() {
      $this->modifyUser(["money" => 1], function() {
        $this->model->buyMeal(1);
      });
    }, InsufficientFundsException::class);
  }
}

$test = new TavernTest();
$test->run();
?>