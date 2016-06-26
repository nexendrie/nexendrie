<?php
namespace Nexendrie\Components;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HelpControl extends Book\BookControl {
  /** @var \Nexendrie\Model\Group */
  protected $groupModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function __construct(\Nexendrie\Model\Group $groupModel, \Nexendrie\Orm\Model $orm, \Nexendrie\Model\Locale $localeModel) {
    $this->groupModel = $groupModel;
    $this->orm = $orm;
    $this->localeModel = $localeModel;
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
    $storage[] = new Book\BookPage("castle", "Hrad");
    $storage[] = new Book\BookPage("monastery", "Klášter");
    $storage[] = new Book\BookPage("house", "Dům");
    $storage[] = new Book\BookPage("money", "Peníze");
    $storage[] = new Book\BookPage("work", "Práce");
    $storage[] = new Book\BookPage("adventures", "Dobrodružství");
    $storage[] = new Book\BookPage("bank", "Banka");
    $storage[] = new Book\BookPage("academy", "Akademie");
    $storage[] = new Book\BookPage("market", "Tržiště");
    $storage[] = new Book\BookPage("stables", "Stáje");
    $storage[] = new Book\BookPage("guild", "Cechy");
    $storage[] = new Book\BookPage("order", "Řády");
    $storage[] = new Book\BookPage("marriage", "Manželství");
    return $storage;
  }
  
  /**
   * @return void
   */
  function renderWork() {
    $this->template->jobs = array();
    $jobs = $this->orm->jobs->findAll()
      ->orderBy("level")
      ->orderBy("neededSkillLevel")
      ->orderBy("count")
      ->orderBy("award");
    foreach($jobs as $job) {
      $j = (object) array(
        "name" => $job->name, "skillName" => $job->neededSkill->name,
        "skillLevel" => $job->neededSkillLevel, "count" => $job->count,
        "award" => $job->awardT, "shift" => $job->shift
      );
      $j->rank = $this->groupModel->getByLevel($job->level)->singleName;
      $this->template->jobs[] = $j;
    }
  }
  
  /**
   * @return void
   */
  function renderAcademy() {
    $this->template->skills = $this->orm->skills->findAll()
      ->orderBy("type")
      ->orderBy("maxLevel")
      ->orderBy("price");
  }
  
  /**
   * @return void
   */
  function renderMonastery() {
    $this->template->maxLevel = \Nexendrie\Orm\Monastery::MAX_LEVEL;
  }
  
  /**
   * @return void
   */
  function renderCastle() {
    $this->template->maxLevel = \Nexendrie\Orm\Castle::MAX_LEVEL;
    $this->template->taxBonusPerLevel = $this->localeModel->money(\Nexendrie\Orm\Castle::TAX_BONUS_PER_LEVEL);
  }
  
  /**
   * @return void
   */
  function renderHouse() {
    $this->template->maxLevel = \Nexendrie\Orm\House::MAX_LEVEL;
    $this->template->incomeBonusPerLevel = \Nexendrie\Orm\House::INCOME_BONUS_PER_LEVEL;
  }
  
  /**
   * @return void
   */
  function renderGuild() {
    $this->template->ranks = $this->orm->guildRanks->findAll();
    $this->template->maxLevel = \Nexendrie\Orm\Guild::MAX_LEVEL;
  }
  
  /**
   * @return void
   */
  function renderOrder() {
    $this->template->ranks = $this->orm->orderRanks->findAll();
    $this->template->maxLevel = \Nexendrie\Orm\Order::MAX_LEVEL;
  }
}

interface HelpControlFactory {
  /** @return HelpControl */
  function create();
}
?>