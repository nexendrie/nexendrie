<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

class ArraysTest extends \Tester\TestCase {
  /**
   * @return string[]
   */
  protected function getInput(): array {
    return [
      ["a" => 1],
      ["a" => 2],
      ["a" => -1],
    ];
  }
  
  public function testOrderByDesc() {
    $input = $this->getInput();
    $output = Arrays::orderby($input, "a", SORT_DESC);
    Assert::type("array", $output);
    Assert::count(3, $output);
    Assert::same(2, $output[0]["a"]);
    Assert::same(1, $output[1]["a"]);
    Assert::same(-1, $output[2]["a"]);
  }
  
  public function testOrderByAsc() {
    $input = $this->getInput();
    $output = Arrays::orderby($input, "a", SORT_ASC);
    Assert::type("array", $output);
    Assert::count(3, $output);
    Assert::same(-1, $output[0]["a"]);
    Assert::same(1, $output[1]["a"]);
    Assert::same(2, $output[2]["a"]);
  }
}

$test = new ArraysTest;
$test->run();
?>