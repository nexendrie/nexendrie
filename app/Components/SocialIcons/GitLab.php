<?php
declare(strict_types=1);

namespace Nexendrie\Components\SocialIcons;

/**
 * GitLab Social Icon
 *
 * @author Jakub Konečný
 */
final class GitLab implements \Nexendrie\Components\ISocialIcon {
  public function getLink(): string {
    return "https://gitlab.com/nexendrie/nexendrie";
  }

  public function getImage(): string {
    return "gitlab-icon.png";
  }

  public function getImageAlt(): string {
    return "Gitlab";
  }

  public function getImageTitle(): string {
    return "Zdrojové kódy a issue tracker";
  }
}
?>