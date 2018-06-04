<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\BookComponent\BookControl;
use Nexendrie\BookComponent\BookPage;
use Nette\Localization\ITranslator;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class HistoryControl extends BookControl {
  public function __construct(ITranslator $translator) {
    parent::__construct(":Front:History", __DIR__ . "/history", $translator);
    $this->pages[] = new BookPage("ancient", "Dávné časy");
    $this->pages[] = new BookPage("empire", "Čas císařství");
    $this->pages[] = new BookPage("principalities", "Éra knížectví");
    $this->pages[] = new BookPage("unification", "Sjednocování");
    $this->pages[] = new BookPage("greatwar", "Velká válka");
    $this->pages[] = new BookPage("afterwar", "Po válce");
  }
}
?>