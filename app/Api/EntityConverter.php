<?php
declare(strict_types=1);

namespace Nexendrie\Api;

use Nexendrie\Api\Transformers\ITransformer;
use Nexendrie\Utils\Collection;
use Nextras\Orm\Entity\Entity;

final class EntityConverter {
  use \Nette\SmartObject;

  protected int $maxDepth;
  /** @var ITransformer[]|Collection */
  protected Collection $transformers;

  /**
   * @param ITransformer[] $transformers
   */
  public function __construct(int $maxDepth, array $transformers) {
    $this->maxDepth = $maxDepth;
    $this->transformers = new class extends Collection {
      protected $class = ITransformer::class;
    };
    foreach($transformers as $transformer) {
      $this->transformers[] = $transformer;
    }
  }

  public function convertEntity(Entity $entity, string $apiVersion): \stdClass {
    /** @var ITransformer|null $transformer */
    $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($entity)]);
    if($transformer === null) {
      return new \stdClass();
    }
    return $transformer->transform($entity, $this->maxDepth, $apiVersion);
  }

  /**
   * @return \stdClass[]
   */
  public function convertCollection(iterable $collection, string $apiVersion): array {
    $result = [];
    foreach($collection as $item) {
      $result[] = $this->convertEntity($item, $apiVersion);
    }
    return $result;
  }
}
?>