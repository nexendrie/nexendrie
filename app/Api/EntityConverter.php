<?php
declare(strict_types=1);

namespace Nexendrie\Api;

use Nexendrie\Api\Transformers\ITransformer;
use Nexendrie\Utils\Collection;
use Nextras\Orm\Entity\Entity;

final class EntityConverter {
  use \Nette\SmartObject;

  /** @var int */
  protected $maxDepth = 2;
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

  public function convertEntity(Entity $entity): \stdClass {
    /** @var ITransformer|null $transformer */
    $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($entity)]);
    if(is_null($transformer)) {
      return new \stdClass();
    }
    return $transformer->transform($entity, $this->maxDepth);
  }

  /**
   * @return \stdClass[]
   */
  public function convertCollection(iterable $collection): array {
    $result = [];
    foreach($collection as $item) {
      $result[] = $this->convertEntity($item);
    }
    return $result;
  }
}
?>