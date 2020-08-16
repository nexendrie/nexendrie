<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\BookComponent\BookControl;
use Nexendrie\BookComponent\BookPage;
use Nexendrie\BookComponent\BookPagesStorage;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 * @property BookPagesStorage|BookPage[] $pages
 */
final class HistoryControl extends BookControl {
  public function __construct() {
    parent::__construct(":Front:History", __DIR__ . "/history");
    $this->pages[] = new BookPage("ancient", "Dávné časy");
    $this->pages[] = new BookPage("empire", "Čas císařství");
    $this->pages[] = new BookPage("principalities", "Éra knížectví");
    $this->pages[] = new BookPage("unification", "Sjednocování");
    $this->pages[] = new BookPage("greatwar", "Velká válka");
    $this->pages[] = new BookPage("afterwar", "Po válce");
  }
}
?>