<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\BookComponent\BookControl;
use Nexendrie\BookComponent\BookPage;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Model\Locale;
use Nexendrie\Model\SettingsRepository;
use Nexendrie\Orm\Monastery as MonasteryEntity;
use Nexendrie\Orm\Castle as CastleEntity;
use Nexendrie\Orm\House as HouseEntity;
use Nexendrie\Orm\Guild as GuildEntity;
use Nexendrie\Orm\Order as OrderEntity;
use Nexendrie\Orm\Marriage as MarriageEntity;
use Nexendrie\Orm\Group as GroupEntity;
use Nexendrie\Orm\Mount as MountEntity;
use Nette\Localization\ITranslator;

/**
 * HelpControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class HelpControl extends BookControl {
  /** @var ORM */
  protected $orm;
  /** @var Locale */
  protected $localeModel;
  /** @var SettingsRepository */
  protected $sr;
  
  public function __construct(ORM $orm, Locale $localeModel, SettingsRepository $sr, ITranslator $translator) {
    parent::__construct(":Front:Help", __DIR__ . "/help", $translator);
    $this->orm = $orm;
    $this->localeModel = $localeModel;
    $this->sr = $sr;
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
      /** @var \Nexendrie\Orm\Group $group */
      $group = $this->orm->groups->getByLevel($job->level);
      $j->rank = $group->singleName;
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
    $this->template->maxAltairLevel = MonasteryEntity::MAX_LEVEL;
    $this->template->maxLibraryLevel = MonasteryEntity::MAX_LEVEL - 1;
    $this->template->basePrayerLife = MonasteryEntity::BASE_PRAYER_LIFE;
    $this->template->prayerLifePerLevel = MonasteryEntity::PRAYER_LIFE_PER_LEVEL;
    $this->template->skillLearningDiscountPerLevel = MonasteryEntity::SKILL_LEARNING_DISCOUNT_PER_LEVEL;
    $this->template->weeklyWearingOut = $this->sr->settings["buildings"]["weeklyWearingOut"];
    $this->template->criticalCondition = $this->sr->settings["buildings"]["criticalCondition"];
  }
  
  public function renderCastle(): void {
    $this->template->maxLevel = CastleEntity::MAX_LEVEL;
    $this->template->taxBonusPerLevel = $this->localeModel->money(CastleEntity::TAX_BONUS_PER_LEVEL);
    $this->template->weeklyWearingOut = $this->sr->settings["buildings"]["weeklyWearingOut"];
    $this->template->criticalCondition = $this->sr->settings["buildings"]["criticalCondition"];
  }
  
  public function renderHouse(): void {
    $this->template->maxLevel = HouseEntity::MAX_LEVEL;
    $this->template->incomeBonusPerLevel = HouseEntity::INCOME_BONUS_PER_LEVEL;
    $this->template->weeklyWearingOut = $this->sr->settings["buildings"]["weeklyWearingOut"];
    $this->template->criticalCondition = $this->sr->settings["buildings"]["criticalCondition"];
  }
  
  public function renderGuild(): void {
    $this->template->ranks = $this->orm->guildRanks->findAll();
    $this->template->maxLevel = GuildEntity::MAX_LEVEL;
    $this->template->incomeBonusPerLevel = GuildEntity::JOB_INCOME_BONUS_PER_LEVEL;
  }
  
  public function renderOrder(): void {
    $this->template->ranks = $this->orm->orderRanks->findAll();
    $this->template->maxLevel = OrderEntity::MAX_LEVEL;
    $this->template->incomeBonusPerLevel = OrderEntity::ADVENTURE_INCOME_BONUS_PER_LEVEL;
  }
  
  public function renderTowns(): void {
    /** @var \Nexendrie\Orm\Town $startingTown */
    $startingTown = $this->orm->towns->getById($this->sr->settings["newUser"]["town"]);
    $this->template->startingTown = $startingTown;
  }
  
  public function renderMarriage(): void {
    $amount = MarriageEntity::HP_INCREASE_PER_LEVEL;
    $this->template->hpIncreasePerLevel = $this->localeModel->hitpoints($amount);
    $this->template->intimacyForLevel = MarriageEntity::INTIMACY_FOR_LEVEL;
  }
  
  public function renderStables(): void {
    $this->template->autoFeedingCost = $this->sr->settings["fees"]["autoFeedMount"];
    $this->template->trainingHpDecrease = MountEntity::HP_DECREASE_TRAINING;
  }
  
  public function renderBank(): void {
    $this->template->loanInterest = $this->sr->settings["fees"]["loanInterest"];
    $this->template->depositInterest = $this->sr->settings["fees"]["depositInterest"];
    $this->template->groups = $this->orm->groups->findBy([
      "level>" => 0,
      "level<" => 10001,
    ])->orderBy("level");
  }

  public function renderTitles(): void {
    $groups = $this->orm->groups->findBy(["level>" => 0])->orderBy("level");
    $this->template->cityGroups = $groups->findBy(["path" => GroupEntity::PATH_CITY]);
    $this->template->towerGroups = $groups->findBy(["path" => GroupEntity::PATH_TOWER]);
    $this->template->churchGroups = $groups->findBy(["path" => GroupEntity::PATH_CHURCH]);
  }

  public function renderAdventures(): void {
    $this->template->hpDecrease = MountEntity::HP_DECREASE_ADVENTURE;
  }
}
?>