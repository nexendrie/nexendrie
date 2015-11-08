<?php
namespace Nexendrie\Components;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HelpControl extends Book\BookControl {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
    parent::__construct(":Front:Help", "help");
  }
  
  /**
   * @return \Nexendrie\Components\Book\BookPagesStorage
   */
  function getPages() {
    $storage = new Book\BookPagesStorage;
    $storage[] = new Book\BookPage("introduction", "Úvod");
    $storage[] = new Book\BookPage("titles", "Tituly");
    $storage[] = new Book\BookPage("towns", "Města");
    $storage[] = new Book\BookPage("monastery", "Klášter");
    $storage[] = new Book\BookPage("money", "Peníze");
    $storage[] = new Book\BookPage("work", "Práce");
    $storage[] = new Book\BookPage("adventures", "Dobrodružství");
    $storage[] = new Book\BookPage("bank", "Banka");
    $storage[] = new Book\BookPage("academy", "Akademie");
    $storage[] = new Book\BookPage("market", "Tržiště");
    $storage[] = new Book\BookPage("stables", "Stáje");
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
}

interface HelpControlFactory {
  /** @return HelpControl */
  function create();
}
?>