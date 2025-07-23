<?php
declare(strict_types=1);

namespace Nexendrie\Api;

require __DIR__ . "/../../bootstrap.php";

use Nexendrie\Orm\Castle;
use Tester\Assert;

final class EntityConverterTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  protected EntityConverter $model;
  protected \Nexendrie\Orm\Model $orm;

  protected function setUp(): void {
    $this->model = $this->getService(EntityConverter::class); // @phpstan-ignore assign.propertyType
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
  }

  public function testConvertEntity(): void {
    $apiVersion = "v1";
    $entity  = new \Nexendrie\Orm\PollVote();
    Assert::type(\stdClass::class, $this->model->convertEntity($entity, $apiVersion));
    /** @var Castle $entity */
    $entity = $this->orm->castles->getById(1);
    $result = $this->model->convertEntity($entity, $apiVersion);
    Assert::type(\stdClass::class, $result);
    Assert::same($entity->id, $result->id);
    Assert::same($entity->name, $result->name);
    Assert::same($entity->description, $result->description);
    Assert::same($entity->createdAt, $result->created);
    Assert::type(\stdClass::class, $result->owner);
    Assert::same($entity->owner->id, $result->owner->id);
    Assert::same($entity->owner->group->id, $result->owner->group);
    Assert::same($entity->level, $result->level);
    Assert::same($entity->hp, $result->hp);
  }
}

$test = new EntityConverterTest();
$test->run();
?>