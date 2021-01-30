<?php
declare(strict_types=1);

namespace Nexendrie\Components;

/**
 * SharerControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
class SharerControl extends \Nette\Application\UI\Control {
  /** @var ISharerLink[] */
  private array $links;

  /**
   * @param ISharerLink[] $links
   */
  public function __construct(array $links) {
    $this->links = $links;
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