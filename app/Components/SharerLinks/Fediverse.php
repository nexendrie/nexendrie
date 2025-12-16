<?php
declare(strict_types=1);

namespace Nexendrie\Components\SharerLinks;

use Nexendrie\Components\SharerLink;

final class Fediverse implements SharerLink
{
    public function getShareLink(string $url, string $title): string
    {
        return "";
    }

    public function getSiteName(): string
    {
        return "fediversmíru";
    }

    public function getPlatformName(): string
    {
        return "fediverse";
    }
}
