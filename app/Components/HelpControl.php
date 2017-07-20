<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\BookComponent\BookControl,
    Nexendrie\BookComponent\BookPage,
    Nexendrie\Orm\Model as ORM,
    Nexendrie\Model\Locale,
    Nexendrie\Orm\Monastery as MonasteryEntity,
    Nexendrie\Orm\Castle as CastleEntity,
    Nexendrie\Orm\House as HouseEntity,
    Nexendrie\Orm\Guild as GuildEntity,
    Nexendrie\Orm\Order as OrderEntity,
    Nexendrie\Translation\Translator;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 */
class HelpControl extends BookControl {
  /** @var ORM */
  protected $orm;
  /** @var Locale */
  protected $localeModel;
  
  public function __construct(ORM $orm, Locale $localeModel, Translator $translator) {
    parent::__construct(":Front:Help", __DIR__ . "/help", $translator);
    $this->orm = $orm;
    $this->localeModel = $localeModel;
    $this->pages[] = new BookPage("introduction", "Úvod");
    $this->pages[] = new BookPage("titles", "Tituly");
    $this->pages[] = new BookPage("towns", "Města");
    $this->pages[] = new BookPage("castle", "Hrad");
    $this->pages[] = new BookPage("monastery", "Klášter");
    $this->pages[] = new BookPage("house", "Dům");
    $this->pages[] = new BookPage("money", "Peníze");
    $this->pages[] = new BookPage("work", "Práce");
    $this->pages[] = new BookPage("adventures", "Dobrodružství");
    $this->pages[] = new BookPage("bank", "Banka");
    $this->pages[] = new BookPage("academy", "Akademie");
    $this->pages[] = new BookPage("market", "Tržiště");
    $this->pages[] = new BookPage("stables", "Stáje");
    $this->pages[] = new BookPage("guild", "Cechy");
    $this->pages[] = new BookPage("order", "Řády");
    $this->pages[] = new BookPage("marriage", "Manželství");
  }
  
  public function renderWork(): void {
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
      $j->rank = $this->orm->groups->getByLevel($job->level)->singleName;
      $this->template->jobs[] = $j;
    }
  }
  
  public function renderAcademy(): void {
    $this->template->skills = $this->orm->skills->findAll()
      ->orderBy("type")
      ->orderBy("maxLevel")
      ->orderBy("price");
  }
  
  public function renderMonastery(): void {
    $this->template->maxLevel = MonasteryEntity::MAX_LEVEL;
  }
  
  public function renderCastle(): void {
    $this->template->maxLevel = CastleEntity::MAX_LEVEL;
    $this->template->taxBonusPerLevel = $this->localeModel->money(CastleEntity::TAX_BONUS_PER_LEVEL);
  }
  
  public function renderHouse(): void {
    $this->template->maxLevel = HouseEntity::MAX_LEVEL;
    $this->template->incomeBonusPerLevel = HouseEntity::INCOME_BONUS_PER_LEVEL;
  }
  
  public function renderGuild(): void {
    $this->template->ranks = $this->orm->guildRanks->findAll();
    $this->template->maxLevel = GuildEntity::MAX_LEVEL;
  }
  
  public function renderOrder(): void {
    $this->template->ranks = $this->orm->orderRanks->findAll();
    $this->template->maxLevel = OrderEntity::MAX_LEVEL;
  }
}
?>