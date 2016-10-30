<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\BookComponent\BookControl,
    Nexendrie\BookComponent\BookPagesStorage,
    Nexendrie\BookComponent\BookPage;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HistoryControl extends BookControl {
  function __construct() {
    $this->lang = "cs";
    parent::__construct(":Front:History", __DIR__ . "/history");
  }
  
  /**
   * @return BookPagesStorage
   */
  function getPages(): BookPagesStorage {
    $storage = new BookPagesStorage;
    $storage[] = new BookPage("ancient", "Dávné časy");
    $storage[] = new BookPage("empire", "Čas císařství");
    $storage[] = new BookPage("principalities", "Éra knížectví");
    $storage[] = new BookPage("unification", "Sjednocování");
    $storage[] = new BookPage("greatwar", "Velká válka");
    $storage[] = new BookPage("afterwar", "Po válce");
    return $storage;
  }
}

interface HistoryControlFactory {
  /** @return HistoryControl */
  function create();
}
?>