<?php
declare(strict_types=1);

namespace Nexendrie\Components\SharerLinks;

use Nexendrie\Model\SettingsRepository;

/**
 * Twitter Sharer Link
 *
 * @author Jakub Konečný
 */
final class Twitter implements \Nexendrie\Components\ISharerLink {
  private string $account;

  public function __construct(SettingsRepository $sr) {
    $this->account = $sr->settings["socialAccounts"]["twitter"];
  }

  public function getShareLink(string $url): string {
    return "https://twitter.com/intent/tweet?url=$url&via={$this->account}";
  }

  public function getSiteName(): string {
    return "X (Twitteru)";
  }

  public function getPlatformName(): string {
    return "twitter";
  }
}
?>