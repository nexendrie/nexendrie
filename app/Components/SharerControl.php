<?php
declare(strict_types=1);

namespace Nexendrie\Components;

/**
 * SharerControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class SharerControl extends \Nette\Application\UI\Control {
  /**
   * @param ISharerLink[] $links
   */
  public function __construct(private readonly array $links) {
  }

  public function render(string $url, string $contentType = ""): void {
    $this->template->setFile(__DIR__ . "/sharer.latte");
    $this->template->links = $this->links;
    $this->template->url = $url;
    $this->template->contentType = $contentType;
    $this->template->render();
  }
}
?>