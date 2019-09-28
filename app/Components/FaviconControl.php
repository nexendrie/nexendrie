<?php
declare(strict_types=1);

namespace Nexendrie\Components;

final class FaviconControl extends \Nette\Application\UI\Control {
  public function render(): void {
    $this->template->setFile(__DIR__ . "/favicon.latte");
    $this->template->render();
  }
}
?>