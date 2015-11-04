<?php
namespace Nexendrie\Components;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HelpControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * 
   * @return \Nexendrie\Components\Help\HelpPagesStorage
   */
  function getPages() {
    $storage = new Help\HelpPagesStorage;
    $storage[] = new Help\HelpPage("introduction", "Úvod");
    $storage[] = new Help\HelpPage("titles", "Tituly");
    $storage[] = new Help\HelpPage("towns", "Města");
    $storage[] = new Help\HelpPage("monastery", "Klášter");
    $storage[] = new Help\HelpPage("money", "Peníze");
    $storage[] = new Help\HelpPage("work", "Práce");
    $storage[] = new Help\HelpPage("adventures", "Dobrodružství");
    $storage[] = new Help\HelpPage("bank", "Banka");
    $storage[] = new Help\HelpPage("academy", "Akademie");
    $storage[] = new Help\HelpPage("market", "Tržiště");
    $storage[] = new Help\HelpPage("stables", "Stáje");
    return $storage;
  }
  
  /**
   * @return void
   */
  function renderWork() {
    $this->template->jobs = array();
    $jobs = $this->orm->jobs->findAll();
    foreach($jobs as $job) {
      $j = (object) array(
        "name" => $job->name, "skillName" => $job->neededSkill->name,
        "skillLevel" => $job->neededSkillLevel, "count" => $job->count,
        "award" => $job->awardT, "shift" => $job->shift
      );
      $j->rank = $this->orm->groups->getByLevel($job->level)->singleName;
      $this->template->jobs[] = $j;
    }
  }
  
  /**
   * @return void
   */
  function renderAcademy() {
    $this->template->skills = $this->orm->skills->findAll();
  }
  
  /**
   * @param string $page
   * @return void
   */
  function render($page) {
    $template = $this->template;
    $pages = $this->getPages();
    if(!$pages->hasPage($page)) $page = "index";
    if($page === "index") {
      $template->setFile(__DIR__ . "/helpIndex.latte");
    } else {
      $template->setFile(__DIR__ . "/helpPage.latte");
      $template->index = $pages->getIndex($page);
      $template->current = $pages[$template->index];
    }
    $template->pages = $pages;
    $method = "render" . ucfirst($page);
    if(method_exists($this, $method)) call_user_func(array($this, $method));
    $template->render();
  }
}

interface HelpControlFactory {
  /** @return HelpControl */
  function create();
}

namespace Nexendrie\Components\Help;

/**
 * @author Jakub Konečný
 */
class HelpPagesStorage extends \Nette\Utils\ArrayList {
  /**
   * @param mixed $index
   * @param HelpPage $page
   * @throws \InvalidArgumentException
   */
  function offsetSet($index, $page) {
    if(!$page instanceof HelpPage) throw new \InvalidArgumentException("Argument must be of type HelpPage.");
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
class HelpPage extends \Nette\Object {
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