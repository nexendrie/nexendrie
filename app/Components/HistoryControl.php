<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\BookComponent\BookControl,
    Nexendrie\BookComponent\BookPage,
    Nexendrie\Translation\Translator;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template|\stdClass $template
 */
class HistoryControl extends BookControl {
  public function __construct(Translator $translator) {
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