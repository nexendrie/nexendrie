<?php
namespace Nexendrie\Components;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HistoryControl extends Book\BookControl {
  function __construct() {
    parent::__construct(":Front:History", "history");
  }
  
  /**
   * @return \Nexendrie\Components\Book\BookPagesStorage
   */
  function getPages() {
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