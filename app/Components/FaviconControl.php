<?php
declare(strict_types=1);

namespace Nexendrie\Components;

/**
 * FaviconControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class FaviconControl extends \Nette\Application\UI\Control {
  public function render(): void {
    $this->template->setFile(__DIR__ . "/favicon.latte");
    $this->template->render();
  }
}
?>