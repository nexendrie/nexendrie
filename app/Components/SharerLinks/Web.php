<?php
declare(strict_types=1);

namespace Nexendrie\Components\SharerLinks;

final class Web implements \Nexendrie\Components\SharerLink
{
    public function getShareLink(string $url, string $title): string
    {
        return "";
    }

    public function getSiteName(): string
    {
        return "zařízení";
    }

    public function getPlatformName(): string
    {
        return "web";
    }
}
