<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class MountTypeTransformer extends BaseTransformer
{
    protected array $fields = ["id", "maleName", "femaleName", "youngName", "level", "damage", "armor", "price",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\MountType::class;
    }
}
