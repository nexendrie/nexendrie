<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class ItemSetTransformer extends BaseTransformer
{
    protected array $fields = ["id", "name", "weapon", "armor", "helmet", "stat", "bonus",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\ItemSet::class;
    }
}
