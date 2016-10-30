<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\BookComponent\BookControl,
    Nexendrie\BookComponent\BookPagesStorage,
    Nexendrie\BookComponent\BookPage,
    Nexendrie\Model\Group,
    Nexendrie\Orm\Model as ORM,
    Nexendrie\Model\Locale,
    Nexendrie\Orm\Monastery as MonasteryEntity,
    Nexendrie\Orm\Castle as CastleEntity,
    Nexendrie\Orm\House as HouseEntity,
    Nexendrie\Orm\Guild as GuildEntity,
    Nexendrie\Orm\Order as OrderEntity;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HelpControl extends BookControl {
  /** @var Group */
  protected $groupModel;
  /** @var ORM */
  protected $orm;
  /** @var Locale */
  protected $localeModel;
  
  function __construct(Group $groupModel, ORM $orm, Locale $localeModel) {
    $this->groupModel = $groupModel;
    $this->orm = $orm;
    $this->localeModel = $localeModel;
    $this->lang = "cs";
    parent::__construct(":Front:Help", __DIR__ . "/help");
  }
  
  /**
   * @return BookPagesStorage
   */
  function getPages(): BookPagesStorage {
    $storage = new BookPagesStorage;
    $storage[] = new BookPage("introduction", "Úvod");
    $storage[] = new BookPage("titles", "Tituly");
    $storage[] = new BookPage("towns", "Města");
    $storage[] = new BookPage("castle", "Hrad");
    $storage[] = new BookPage("monastery", "Klášter");
    $storage[] = new BookPage("house", "Dům");
    $storage[] = new BookPage("money", "Peníze");
    $storage[] = new BookPage("work", "Práce");
    $storage[] = new BookPage("adventures", "Dobrodružství");
    $storage[] = new BookPage("bank", "Banka");
    $storage[] = new BookPage("academy", "Akademie");
    $storage[] = new BookPage("market", "Tržiště");
    $storage[] = new BookPage("stables", "Stáje");
    $storage[] = new BookPage("guild", "Cechy");
    $storage[] = new BookPage("order", "Řády");
    $storage[] = new BookPage("marriage", "Manželství");
    return $storage;
  }
  
  /**
   * @return void
   */
  function renderWork() {
    $this->template->jobs = [];
    $jobs = $this->orm->jobs->findAll()
      ->orderBy("level")
      ->orderBy("neededSkillLevel")
      ->orderBy("count")
      ->orderBy("award");
    foreach($jobs as $job) {
      $j = (object) [
        "name" => $job->name, "skillName" => $job->neededSkill->name,
        "skillLevel" => $job->neededSkillLevel, "count" => $job->count,
        "award" => $job->awardT, "shift" => $job->shift
      ];
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
    $this->template->maxLevel = MonasteryEntity::MAX_LEVEL;
  }
  
  /**
   * @return void
   */
  function renderCastle() {
    $this->template->maxLevel = CastleEntity::MAX_LEVEL;
    $this->template->taxBonusPerLevel = $this->localeModel->money(CastleEntity::TAX_BONUS_PER_LEVEL);
  }
  
  /**
   * @return void
   */
  function renderHouse() {
    $this->template->maxLevel = HouseEntity::MAX_LEVEL;
    $this->template->incomeBonusPerLevel = HouseEntity::INCOME_BONUS_PER_LEVEL;
  }
  
  /**
   * @return void
   */
  function renderGuild() {
    $this->template->ranks = $this->orm->guildRanks->findAll();
    $this->template->maxLevel = GuildEntity::MAX_LEVEL;
  }
  
  /**
   * @return void
   */
  function renderOrder() {
    $this->template->ranks = $this->orm->orderRanks->findAll();
    $this->template->maxLevel = OrderEntity::MAX_LEVEL;
  }
}

interface HelpControlFactory {
  /** @return HelpControl */
  function create();
}
?>