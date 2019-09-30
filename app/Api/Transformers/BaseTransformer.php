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
  protected $fields = [];
  /** @var string[] */
  protected $fieldsRename = [];
  /** @var \Nette\DI\Container */
  protected $container;
  /** @var ITransformer[]|Collection */
  protected $transformers;
  /** @var LinkGenerator */
  protected $linkGenerator;
  /** @var bool */
  protected $createSelfLink = true;

  public function __construct(\Nette\DI\Container $container, LinkGenerator $linkGenerator) {
    $this->container = $container;
    $this->linkGenerator = $linkGenerator;
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
          $links[$transformer->getCollectionName()] = $this->createEntityLink($transformer->getCollectionName(), $transformer->getCollectionName(), $apiVersion, $entity->id);
          if($maxDepth > 0 && $transformer !== null) {
            $array[] = $transformer->transform($item, $maxDepth, $apiVersion);
          } else {
            $array[] = $item->id;
          }
        }
        $value = $array;
      } else {
        /** @var ITransformer|null $transformer */
        $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($value)]);
        if($maxDepth > 0 && $transformer !== null) {
          $value = $transformer->transform($value, $maxDepth, $apiVersion);
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