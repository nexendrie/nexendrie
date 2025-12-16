<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class GuildRankTransformer extends BaseTransformer
{
    protected array $fields = ["id", "name", "incomeBonus", "guildFee",];
    protected array $fieldsRename = ["guildFee" => "fee",];

    public function getEntityClassName(): string
    {
        return \Nexendrie\Orm\GuildRank::class;
    }
}
