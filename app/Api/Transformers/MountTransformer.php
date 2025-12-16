<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class MountTransformer extends BaseTransformer
{
    protected array $fields = ["id", "name", "gender", "type", "owner", "price", "onMarket", "created", "hp", "damage", "armor",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\Mount::class;
    }
}
