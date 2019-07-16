<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

use Nexendrie\Utils\Collection;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Entity\ToArrayConverter;
use Nextras\Orm\Relationships\HasMany;

abstract class BaseTransformer implements ITransformer {
  use \Nette\SmartObject;

  /** @var string[] */
  protected $fields = [];
  /** @var string[] */
  protected $fieldsRename = [];
  /** @var \Nette\DI\Container */
  protected $container;
  /** @var ITransformer[]|Collection */
  protected $transformers;

  public function __construct(\Nette\DI\Container $container) {
    $this->container = $container;
    $this->transformers = new class extends Collection {
      protected $class = ITransformer::class;
    };
  }

  protected function getTransformers(): void {
    if(count($this->transformers) > 0) {
      return;
    }
    foreach($this->container->findByType(ITransformer::class) as $serviceName) {
      $this->transformers[] = $this->container->getService($serviceName);
    }
  }

  public function transform(Entity $entity, int $maxDepth): \stdClass {
    $this->getTransformers();
    $maxDepth--;
    $record = $entity->toArray(ToArrayConverter::RELATIONSHIP_AS_IS);
    $record = array_filter($record, function($key) {
      return in_array($key, $this->fields, true);
    }, ARRAY_FILTER_USE_KEY);
    foreach($record as &$value) {
      if(!$value instanceof Entity && !$value instanceof HasMany) {
        continue;
      }
      if($value instanceof HasMany) {
        $array = [];
        /** @var Entity $item */
        foreach($value as $item) {
          /** @var ITransformer|null $transformer */
          $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($item)]);
          if($maxDepth > 0 && !is_null($transformer)) {
            $array[] = $transformer->transform($item, $maxDepth);
          } else {
            $array[] = $item->id;
          }
        }
        $value = $array;
      } else {
        /** @var ITransformer|null $transformer */
        $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($value)]);
        if($maxDepth > 0 && !is_null($transformer)) {
          $value = $transformer->transform($value, $maxDepth);
        } else {
          $value = $value->id;
        }
      }
    }
    unset($value);
    foreach($this->fieldsRename as $old => $new) {
      $value = $record[$old];
      unset($record[$old]);
      $record[$new] = $value;
    }
    return (object) $record;
  }
}
?>