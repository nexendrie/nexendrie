<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Article as ArticleEntity,
    Nexendrie\Orm\Punishment as PunishmentEntity;

require __DIR__ . "/../../bootstrap.php";

class ChronicleTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var Chronicle */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Chronicle::class);
  }
  
  function testArticles() {
    $result = $this->model->articles();
    Assert::type(ICollection::class, $result);
  }
  
  function testCrimes() {
    $result = $this->model->crimes();
    Assert::type(ICollection::class, $result);
    Assert::type(PunishmentEntity::class, $result->fetch());
  }
}

$test = new ChronicleTest;
$test->run();
?>