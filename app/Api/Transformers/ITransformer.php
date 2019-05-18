<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

use Nextras\Orm\Entity\Entity;

interface ITransformer {
  public function transform(Entity $entity, int $maxDepth): \stdClass;
  public function getEntityClassName(): string;
}
?>