<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;
use Nexendrie\Forms\UserSettingsFormFactory;

/**
 * User
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $publicname
 * @property string $password
 * @property string $email
 * @property int $joined
 * @property-read string $joinedAt {virtual}
 * @property int $lastActive
 * @property-read string $lastActiveAt {virtual}
 * @property int|null $lastPrayer {default null}
 * @property int|null $lastTransfer {default null}
 * @property Group $group {m:1 Group::$members}
 * @property bool $infomails {default false}
 * @property string $style
 * @property string $gender {enum static::GENDER_*} {default static::GENDER_MALE}
 * @property bool $banned {default false}
 * @property int $life
 * @property-read int $maxLife {virtual}
 * @property int $money {default 2}
 * @property Town $town {m:1 Town::$denizens}
 * @property Monastery|null $monastery {m:1 Monastery::$members} {default null}
 * @property Castle|null $castle {1:1 Castle::$owner} {default null}
 * @property House|null $house {1:1 House::$owner} {default null}
 * @property int $prayers {default 0}
 * @property Guild|null $guild {m:1 Guild::$members} {default null}
 * @property GuildRank|null $guildRank {m:1 GuildRank::$people} {default null}
 * @property Order|null $order {m:1 Order::$members} {default null}
 * @property OrderRank|null $orderRank {m:1 OrderRank::$people} {default null}
 * @property-read string $moneyT {virtual}
 * @property OneHasMany|Comment[] $comments {1:m Comment::$author}
 * @property OneHasMany|Article[] $articles {1:m Article::$author}
 * @property OneHasMany|Poll[] $polls {1:m Poll::$author}
 * @property OneHasMany|Message[] $sentMessages {1:m Message::$from}
 * @property OneHasMany|Message[] $receivedMessages {1:m Message::$to}
 * @property OneHasMany|PollVote[] $pollVotes {1:m PollVote::$user}
 * @property OneHasMany|UserItem[] $items {1:m UserItem::$user}
 * @property OneHasMany|UserJob[] $jobs {1:m UserJob::$user}
 * @property OneHasMany|Town[] $ownedTowns {1:m Town::$owner}
 * @property OneHasMany|Mount[] $mounts {1:m Mount::$owner}
 * @property OneHasMany|UserSkill[] $skills {1:m UserSkill::$user}
 * @property OneHasMany|Punishment[] $punishments {1:m Punishment::$user}
 * @property OneHasMany|Loan[] $loans {1:m Loan::$user}
 * @property OneHasMany|UserAdventure[] $adventures {1:m UserAdventure::$user}
 * @property OneHasMany|Monastery[] $monasteriesLed {1:m Monastery::$leader}
 * @property OneHasMany|MonasteryDonation[] $monasteryDonations {1:m MonasteryDonation::$user}
 * @property OneHasMany|BeerProduction[] $beerProduction {1:m BeerProduction::$user}
 * @property OneHasMany|Marriage[] $sentMarriages {1:m Marriage::$user1}
 * @property OneHasMany|Marriage[] $receivedMarriages {1:m Marriage::$user2}
 * @property OneHasMany|Election[] $receivedVotes {1:m Election::$candidate}
 * @property OneHasMany|Election[] $castedVotes {1:m Election::$voter}
 * @property OneHasMany|ElectionResult[] $elections {1:m ElectionResult::$candidate}
 * @property OneHasMany|Deposit[] $deposits {1:m Deposit::$user}
 * @property OneHasMany|GuildFee[] $guildFees {1:m GuildFee::$user}
 * @property OneHasMany|OrderFee[] $orderFees {1:m OrderFee::$user}
 * @property OneHasMany|ChatMessage[] $chatMessages {1:m ChatMessage::$user}
 * @property-read string $title {virtual}
 * @property-read int $completedAdventures {virtual}
 * @property-read int $completedJobs {virtual}
 * @property-read int $producedBeers {virtual}
 * @property-read int $punishmentsCount {virtual}
 * @property-read int $lessonsTaken {virtual}
 * @property-read int $currentOrderContribution {virtual}
 * @property-read int $currentGuildContribution {virtual}
 * @property-read int $townsOwned {virtual}
 * @property-read int $mountsOwned {virtual}
 */
final class User extends \Nextras\Orm\Entity\Entity {
  public const GENDER_MALE = "male";
  public const GENDER_FEMALE = "female";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Model\SettingsRepository */
  protected $sr;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  public function injectSr(\Nexendrie\Model\SettingsRepository $sr): void {
    $this->sr = $sr;
  }
  
  protected function setterStyle(string $value): string {
    if(array_key_exists($value, UserSettingsFormFactory::getStylesList())) {
      return $value;
    }
    return $this->sr->settings["newUser"]["style"];
  }
  
  protected function getterJoinedAt(): string {
    return $this->localeModel->formatDateTime($this->joined);
  }
  
  protected function getterLastActiveAt(): string {
    return $this->localeModel->formatDateTime($this->lastActive);
  }
  
  /**
   * @return string[]
   */
  public static function getGenders(): array {
    return [
      static::GENDER_MALE => "muž",
      static::GENDER_FEMALE => "žena"
    ];
  }
  
  protected function getterMoneyT(): string {
    return $this->localeModel->money($this->money);
  }
  
  protected function getterMaxLife(): int {
    $maxLife = 60;
    /** @var UserSkill[] $lifeSkills */
    $lifeSkills = $this->skills->get()->findBy(["this->skill->stat" => Skill::STAT_HITPOINTS]);
    foreach($lifeSkills as $skill) {
      $maxLife += $skill->skill->statIncrease * $skill->level;
    }
    return $maxLife;
  }
  
  protected function setterLife(int $value): int {
    return Numbers::range($value, 1, $this->maxLife);
  }
  
  protected function getterTitle(): string {
    if($this->gender === static::GENDER_FEMALE) {
      return $this->group->femaleName;
    }
    return $this->group->singleName;
  }
  
  protected function getterCompletedAdventures(): int {
    return $this->adventures->get()->findBy(["progress" => UserAdventure::PROGRESS_COMPLETED])->countStored();
  }
  
  protected function getterCompletedJobs(): int {
    return $this->jobs->get()->findBy(["finished" => true, "earned>" => 0])->countStored();
  }
  
  protected function getterProducedBeers(): int {
    $amount = 0;
    foreach($this->beerProduction as $row) {
      $amount += $row->amount;
    }
    return $amount;
  }
  
  protected function getterPunishmentsCount(): int {
    return $this->punishments->countStored();
  }
  
  protected function getterLessonsTaken(): int {
    $amount = 0;
    foreach($this->skills as $lesson) {
      $amount += $lesson->level;
    }
    return $amount;
  }
  
  protected function getterCurrentOrderContribution(): int {
    if(is_null($this->order)) {
      return 0;
    }
    /** @var OrderFee|null $record */
    $record = $this->orderFees->get()->getBy(["order" => $this->order]);
    if(is_null($record)) {
      return 0;
    }
    return $record->amount;
  }
  
  protected function getterCurrentGuildContribution(): int {
    if(is_null($this->guild)) {
      return 0;
    }
    /** @var GuildFee|null $record */
    $record = $this->guildFees->get()->getBy(["guild" => $this->guild]);
    if(is_null($record)) {
      return 0;
    }
    return $record->amount;
  }
  
  protected function getterTownsOwned(): int {
    return $this->ownedTowns->countStored();
  }
  
  protected function getterMountsOwned(): int {
    return $this->mounts->countStored();
  }
  
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    $this->joined = $this->lastActive = time();
    $this->life = $this->maxLife;
  }
}
?>