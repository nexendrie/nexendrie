<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

use Nextras\Orm\Entity\Entity;

interface Transformer
{
    public function transform(Entity $entity, int $maxDepth, string $apiVersion): \stdClass;

    /**
     * @return class-string
     */
    public function getEntityClassName(): string;

    /**
     * @return class-string
     */
    public function getCollectionName(): string;
}
