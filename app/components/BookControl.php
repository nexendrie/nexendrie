<?php
namespace Nexendrie\Components\Book;

/**
 * BookControl
 *
 * @author Jakub Konečný
 */
abstract class BookControl extends \Nette\Application\UI\Control {
  /** @var string */
  private $presenterName;
  /** @var string */
  private $folder;
  
  /**
   * @param string $presenterName
   * @param string $folder
   */
  function __construct($presenterName, $folder) {
    $this->presenterName = (string) $presenterName;
    $this->folder = (string) $folder;
  }
  
  /** @return BookPagesStorage */
  abstract function getPages();
  
  /**
   * @param string $page
   * @return void
   */
  function render($page) {
    $template = $this->template;
    $template->presenterName = $this->presenterName;
    $template->folder = $this->folder;
    $pages = $this->getPages();
    if(!$pages->hasPage($page)) $page = "index";
    if($page === "index") {
      $template->setFile(__DIR__ . "/bookIndex.latte");
    } else {
      $template->setFile(__DIR__ . "/bookPage.latte");
      $template->index = $pages->getIndex($page);
      $template->current = $pages[$template->index];
    }
    $template->pages = $pages;
    $method = "render" . ucfirst($page);
    if(method_exists($this, $method)) call_user_func(array($this, $method));
    $template->render();
  }
}

/**
 * @author Jakub Konečný
 */
class BookPagesStorage extends \Nette\Utils\ArrayList {
  /**
   * @param mixed $index
   * @param BookPage $page
   * @throws \InvalidArgumentException
   */
  function offsetSet($index, $page) {
    if(!$page instanceof BookPage) throw new \InvalidArgumentException("Argument must be of type HelpPage.");
    elseif($this->hasPage($page->slug)) throw new \RuntimeException("Duplicate slug $page->slug.");
    parent::offsetSet($index, $page);
  }
  
  /**
   * @param string $slug
   * @return bool
   */
  function hasPage($slug) {
    foreach($this as $page) {
      if($page->slug === $slug) return true;
    }
    return false;
  }
  
  /**
   * @param string $slug
   * @return int|NULL
   */
  function getIndex($slug) {
    foreach($this as $index => $page) {
      if($page->slug === $slug) return $index;
    }
    return NULL;
  }
}

/**
 * @author Jakub Konečný
 * @property-read string $slug
 * @property-read string $title
 */
class BookPage extends \Nette\Object {
  /** @var string */
  protected $slug;
  /** @var string */
  protected $title;
  
  /**
   * @param string $slug
   * @param string $title
   */
  function __construct($slug, $title) {
    $this->slug = $slug;
    $this->title = $title;
  }
  
  function getSlug() {
    return $this->slug;
  }

  function getTitle() {
    return $this->title;
  }
}
?>