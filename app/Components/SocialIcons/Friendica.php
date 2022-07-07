<?php
declare(strict_types=1);

namespace Nexendrie\Components\SocialIcons;

use Nexendrie\Model\SettingsRepository;

/**
 * Twitter Social Icon
 *
 * @author Jakub Konečný
 */
final class Friendica implements \Nexendrie\Components\ISocialIcon {
  private string $account;

  public function __construct(SettingsRepository $sr) {
    $this->account = $sr->settings["socialAccounts"]["friendica"];
  }

  public function getLink(): string {
    $parts = explode("@", $this->account);
    return "https://{$parts[2]}/profile/{$parts[1]}";
  }

  public function getImage(): string {
    return "friendica.png";
  }

  public function getImageAlt(): string {
    return "Friendica";
  }

  public function getImageTitle(): string {
    return "Účet Friendica v rámci fedivesmíru";
  }
}
?>