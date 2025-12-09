<?php
declare(strict_types=1);

namespace Nexendrie\Components\SharerLinks;

use Nexendrie\Components\ISharerLink;

final class Fediverse implements ISharerLink {

  public function getShareLink(string $url, string $title): string {
    return "";
  }

  public function getSiteName(): string {
    return "fediversmíru";
  }

  public function getPlatformName(): string {
    return "fediverse";
  }
}
?>