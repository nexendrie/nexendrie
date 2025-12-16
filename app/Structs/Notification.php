<?php
declare(strict_types=1);

namespace Nexendrie\Structs;

final class Notification
{
    public string $title = "";
    public string $body = "";
    public string $lang = "cs-CZ";
    public ?string $icon = null;
    public string $tag = "";
    public ?string $targetUrl = null;
}
