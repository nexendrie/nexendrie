<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

use Nette\Application\LinkGenerator;
use Nette\Utils\Strings;
use Nexendrie\Utils\Collection;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Entity\ToArrayConverter;
use Nextras\Orm\Relationships\HasMany;

abstract class BaseTransformer implements ITransformer {
  use \Nette\SmartObject;

  /** @var string[] */
  protected array $fields = [];
  /** @var string[] */
  protected array $fieldsRename = [];
  /** @var ITransformer[]|Collection */
  protected Collection $transformers;
  protected bool $createSelfLink = true;

  public function __construct(protected \Nette\DI\Container $container, protected LinkGenerator $linkGenerator) {
    $this->transformers = new class extends Collection {
      protected string $class = ITransformer::class;
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

  public function getCollectionName(): string {
    $entityName = (string) Strings::after($this->getEntityClassName(), "\\", -1) . "s";
    return Strings::firstLower($entityName);
  }

  public function transform(Entity $entity, int $maxDepth, string $apiVersion): \stdClass {
    $this->getTransformers();
    $maxDepth--;
    $links = [];
    if($this->createSelfLink) {
      $links["self"] = $this->createEntityLink("self", $this->getCollectionName(), $apiVersion, $entity->id, $entity->id);
    }
    $record = $entity->toArray(ToArrayConverter::RELATIONSHIP_AS_IS);
    $record = array_filter($record, function($key): bool {
      return in_array($key, $this->fields, true);
    }, ARRAY_FILTER_USE_KEY);
    foreach($record as $rel => &$value) {
      if(!$value instanceof Entity && !$value instanceof HasMany) {
        continue;
      }
      if($value instanceof HasMany) {
        $array = [];
        /** @var Entity $item */
        foreach($value as $item) {
          /** @var ITransformer|null $transformer */
          $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($item)]);
          if($transformer === null || $maxDepth < 1) {
            $array[] = $item->id;
            continue;
          }
          $links[$transformer->getCollectionName()] = $this->createEntityLink($transformer->getCollectionName(), $transformer->getCollectionName(), $apiVersion, $entity->id);
          $array[] = $transformer->transform($item, $maxDepth, $apiVersion);
        }
        $value = $array;
      } else {
        /** @var ITransformer|null $transformer */
        $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($value)]);
        if($transformer === null || $maxDepth < 1) {
          $value = $value->id;
          continue;
        }
        $links[$rel] = $this->createEntityLink($rel, $transformer->getCollectionName(), $apiVersion, $entity->id, $value->id);
        $value = $transformer->transform($value, $maxDepth, $apiVersion);
      }
    }
    unset($value);
    foreach($this->fieldsRename as $old => $new) {
      $value = $record[$old];
      unset($record[$old]);
      $record[$new] = $value;
    }
    if(count($links) > 0) {
      $record["_links"] = array_values($links);
    }
    return (object) $record;
  }

  protected function createEntityLink(string $rel, string $targetCollectionName, string $apiVersion, int $currentId, ?int $id = null): \stdClass {
    $targetCollectionName = Strings::firstUpper($targetCollectionName);
    $apiVersion = Strings::firstUpper($apiVersion);
    if($id === null) {
      $params = ["associations" => [$this->getCollectionName() => $currentId]];
      $action = "readAll";
    } else {
      $params = ["id" => $id];
      $action = "read";
    }
    return (object) [
      "rel" => $rel,
      "href" => $this->linkGenerator->link("Api:$apiVersion:$targetCollectionName:$action", $params),
    ];
  }
}
?>