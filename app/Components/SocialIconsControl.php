<?php
declare(strict_types=1);

namespace Nexendrie\Components;

/**
 * SocialIconsControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
class SocialIconsControl extends \Nette\Application\UI\Control {
  /** @var ISocialIcon[] */
  private array $icons;

  /**
   * @param ISocialIcon[] $icons
   */
  public function __construct(array $icons) {
    $this->icons = $icons;
  }

  public function render(): void {
    $this->template->setFile(__DIR__ . "/socialIcons.latte");
    $this->template->icons = $this->icons;
    $this->template->render();
  }
}
?>