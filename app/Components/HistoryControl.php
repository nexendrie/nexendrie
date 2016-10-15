<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use \Nexendrie\BookComponent as Book;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HistoryControl extends Book\BookControl {
  function __construct() {
    $this->lang = "cs";
    parent::__construct(":Front:History", __DIR__ . "/history");
  }
  
  /**
   * @return Book\BookPagesStorage
   */
  function getPages(): Book\BookPagesStorage {
    $storage = new Book\BookPagesStorage;
    $storage[] = new Book\BookPage("ancient", "Dávné časy");
    $storage[] = new Book\BookPage("empire", "Čas císařství");
    $storage[] = new Book\BookPage("principalities", "Éra knížectví");
    $storage[] = new Book\BookPage("unification", "Sjednocování");
    $storage[] = new Book\BookPage("greatwar", "Velká válka");
    $storage[] = new Book\BookPage("afterwar", "Po válce");
    return $storage;
  }
}

interface HistoryControlFactory {
  /** @return HistoryControl */
  function create();
}
?>