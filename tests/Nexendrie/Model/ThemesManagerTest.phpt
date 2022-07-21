<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";


final class ThemesManagerTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  private ThemesManager $model;
  
  protected function setUp() {
    $this->model = $this->getService(ThemesManager::class);
  }
  
  public function testGetList() {
    $result = $this->model->getList();
    Assert::same([
      "dark-sky" => "Temná obloha (zavržený)",
      "nexendrie" => "Nexendrie",
      "blue-sky" => "Modrá obloha (zavržený)",
      "matrix" => "Matrix (experimentální)",
    ], $result);
  }
}

$test = new ThemesManagerTest();
$test->run();
?>