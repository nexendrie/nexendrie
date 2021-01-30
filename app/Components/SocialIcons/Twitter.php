<?php
declare(strict_types=1);

namespace Nexendrie\Components\SocialIcons;

use Nexendrie\Model\SettingsRepository;

/**
 * Twitter Social Icon
 *
 * @author Jakub Konečný
 */
final class Twitter implements \Nexendrie\Components\ISocialIcon {
  private string $account;

  public function __construct(SettingsRepository $sr) {
    $this->account = $sr->settings["socialAccounts"]["twitter"];
  }

  public function getLink(): string {
    return "https://twitter.com/{$this->account}";
  }

  public function getImage(): string {
    return "twitter.png";
  }

  public function getImageAlt(): string {
    return "Twitter";
  }

  public function getImageTitle(): string {
    return "Twitter";
  }
}
?>