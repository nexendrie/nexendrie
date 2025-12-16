<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class MealTransformer extends BaseTransformer
{
    protected array $fields = ["id", "name", "message", "price", "life",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\Meal::class;
    }
}
