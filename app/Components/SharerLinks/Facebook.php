<?php
declare(strict_types=1);

namespace Nexendrie\Components\SharerLinks;

/**
 * Facebook Sharer Link
 *
 * @author Jakub Konečný
 */
final class Facebook implements \Nexendrie\Components\ISharerLink {
  public function getShareLink(string $url, string $title): string {
    return "https://www.facebook.com/sharer/sharer.php?u=$url";
  }

  public function getSiteName(): string {
    return "Facebooku";
  }

  public function getPlatformName(): string {
    return "facebook";
  }
}
?>