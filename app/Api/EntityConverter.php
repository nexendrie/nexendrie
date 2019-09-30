<?php
declare(strict_types=1);

namespace Nexendrie\Api;

use Nexendrie\Api\Transformers\ITransformer;
use Nexendrie\Utils\Collection;
use Nextras\Orm\Entity\Entity;

final class EntityConverter {
  use \Nette\SmartObject;

  /** @var int */
  protected $maxDepth;
  /** @var ITransformer[]|Collection */
  protected $transformers;

  public function __construct(int $maxDepth, \Nette\DI\Container $container) {
    $this->maxDepth = $maxDepth;
    $this->transformers = new class extends Collection {
      protected $class = ITransformer::class;
    };
    foreach($container->findByType(ITransformer::class) as $serviceName) {
      $this->transformers[] = $container->getService($serviceName);
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