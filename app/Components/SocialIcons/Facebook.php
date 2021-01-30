<?php
declare(strict_types=1);

namespace Nexendrie\Components\SocialIcons;

use Nexendrie\Model\SettingsRepository;

/**
 * Facebook Social Icon
 *
 * @author Jakub Konečný
 */
final class Facebook implements \Nexendrie\Components\ISocialIcon {
  private string $account;

  public function __construct(SettingsRepository $sr) {
    $this->account = $sr->settings["socialAccounts"]["facebook"];
  }

  public function getLink(): string {
    return "https://www.facebook.com/{$this->account}/";
  }

  public function getImage(): string {
    return "facebook-logo.png";
  }

  public function getImageAlt(): string {
    return "Facebook";
  }

  public function getImageTitle(): string {
    return "Facebook stránka";
  }
}
?>