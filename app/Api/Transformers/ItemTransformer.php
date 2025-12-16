<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class ItemTransformer extends BaseTransformer
{
    protected array $fields = ["id", "name", "description", "price", "shop", "type", "strength",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\Item::class;
    }
}
