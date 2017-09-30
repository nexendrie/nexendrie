<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;


/**
 * User
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $username
 * @property string $publicname
 * @property string $password
 * @property string $email
 * @property int $joined
 * @property-read string $joinedAt {virtual}
 * @property int $lastActive
 * @property-read string $lastActiveAt {virtual}
 * @property int|NULL $lastPrayer {default NULL}
 * @property int|NULL $lastTransfer {default NULL} 
 * @property Group $group {m:1 Group::$members}
 * @property bool $infomails {default false}
 * @property string $style {default "blu-sky"}
 * @property string $gender {enum self::GENDER_*} {default self::GENDER_MALE}
 * @property bool $banned {default false}
 * @property int $life {default 60}
 * @property int $maxLife {default 60}
 * @property int $money {default 2}
 * @property Town $town {m:1 Town::$denizens}
 * @property Monastery|NULL $monastery {m:1 Monastery::$members} {default NULL}
 * @property Castle|NULL $castle {1:1 Castle::$owner} {default NULL}
 * @property House|NULL $house {1:1 House::$owner} {default NULL}
 * @property int $prayers {default 0}
 * @property Guild|NULL $guild {m:1 Guild::$members} {default NULL}
 * @property GuildRank|NULL $guildRank {m:1 GuildRank::$people} {default NULL}
 * @property Order|NULL $order {m:1 Order::$members} {default NULL}
 * @property OrderRank|NULL $orderRank {m:1 OrderRank::$people} {default NULL}
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
 * @property OneHasMany|UserSkill $skills {1:m UserSkill::$user}
 * @property OneHasMany|Punishment[] $punishments {1:m Punishment::$user}
 * @property OneHasMany|Loan[] $loans {1:m Loan::$user}
 * @property OneHasMany|UserAdventure $adventures {1:m UserAdventure::$user}
 * @property OneHasMany|Monastery[] $monasteriesLed {1:m Monastery::$leader}
 * @property OneHasMany|MonasteryDonation[] $monasteryDonations {1:m MonasteryDonation::$user}
 * @property OneHasMany|BeerProduction[] $beerProduction {1:m BeerProduction::$user}
 * @property OneHasMany|Marriage[] $sentMarriages {1:m Marriage::$user1}
 * @property OneHasMany|Marriage[] $recievedMarriages {1:m Marriage::$user2}
 * @property OneHasMany|Election[] $receivedVotes {1:m Election::$candidate}
 * @property OneHasMany|Election[] $castedVotes {1:m Election::$voter}
 * @property OneHasMany|ElectionResult[] $elections {1:m ElectionResult::$candidate}
 * @property-read string $title {virtual}
 * @property-read int $completedAdventures {virtual}
 * @property-read int $producedBeers {virtual}
 * @property-read int $punishmentsCount {virtual}
 * @property-read int $lessonsTaken {virtual}
 * @property-read int[] $messagesCount {virtual}
 */
class User extends \Nextras\Orm\Entity\Entity {
  public const GENDER_MALE = "male";
  public const GENDER_FEMALE = "female";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterJoinedAt(): string {
    return $this->localeModel->formatDate($this->joined);
  }
  
  protected function getterLastActiveAt() {
    return $this->localeModel->formatDate($this->lastActive);
  }
  
  /**
   * @return string[]
   */
  public static function getGenders(): array {
    return [
      self::GENDER_MALE => "muž",
      self::GENDER_FEMALE => "žena"
    ];
  }
  
  protected function getterMoneyT(): string {
    return $this->localeModel->money($this->money);
  }
  
  protected function setterLife(int $value): int {
    if($value > $this->maxLife) {
      return $this->maxLife;
    } elseif($value < 1) {
      return 1;
    }
    return $value;
  }
  
  protected function getterTitle(): string {
    if($this->gender === self::GENDER_FEMALE) {
      return $this->group->femaleName;
    }
    return $this->group->singleName;
  }
  
  protected function getterCompletedAdventures() {
    return $this->adventures->get()->findBy(["progress" => 10])->countStored();
  }
  
  protected function getterProducedBeers() {
    $amount = 0;
    foreach($this->beerProduction as $row) {
      $amount += $row->amount;
    }
    return $amount;
  }
  
  protected function getterPunishmentsCount() {
    return $this->punishments->countStored();
  }
  
  protected function getterLessonsTaken() {
    $amount = 0;
    foreach($this->skills as $lesson) {
      $amount += $lesson->level;
    }
    return $amount;
  }
  
  protected function getterMessagesCount() {
    return ["sent" => $this->sentMessages->get()->countStored(), "recieved" => $this->receivedMessages->get()->countStored()];
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->joined = $this->lastActive = time();
  }
}
?>