<?php
declare(strict_types=1);

namespace Nexendrie\Api;

use Nexendrie\Api\Transformers\Transformer;
use Nexendrie\Utils\Collection;
use Nextras\Orm\Entity\Entity;

final class EntityConverter
{
    private int $maxDepth;
    /** @var Collection<Transformer> */
    private Collection $transformers;

    /**
     * @param list<Transformer> $transformers
     */
    public function __construct(int $maxDepth, array $transformers)
    {
        $this->maxDepth = $maxDepth;
        // @phpstan-ignore assign.propertyType
        $this->transformers = new class extends Collection {
            public function __construct()
            {
                parent::__construct();
                $this->class = Transformer::class;
            }
        };
        foreach ($transformers as $transformer) {
            $this->transformers[] = $transformer;
        }
    }

    public function convertEntity(Entity $entity, string $apiVersion): \stdClass
    {
        /** @var Transformer|null $transformer */
        $transformer = $this->transformers->getItem(["getEntityClassName()" => get_class($entity)]);
        if ($transformer === null) {
            return new \stdClass();
        }
        return $transformer->transform($entity, $this->maxDepth, $apiVersion);
    }

    /**
     * @return \stdClass[]
     */
    public function convertCollection(iterable $collection, string $apiVersion): array
    {
        $result = [];
        foreach ($collection as $item) {
            $result[] = $this->convertEntity($item, $apiVersion);
        }
        return $result;
    }
}
