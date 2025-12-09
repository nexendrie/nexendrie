<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface ISharerLink {
  public function getShareLink(string $url, string $title): string;
  public function getSiteName(): string;
  public function getPlatformName(): string;
}
?>