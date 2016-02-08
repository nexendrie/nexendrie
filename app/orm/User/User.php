<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;


/**
 * User
 *
 * @author Jakub Konečný
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
 * @property bool $infomails {default 0}
 * @property string $style {default "blu-sky"}
 * @property string $gender {enum self::GENDER_*} {default self::GENDER_MALE}
 * @property bool $banned {default 0}
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
 */
class User extends \Nextras\Orm\Entity\Entity {
  const GENDER_MALE = "male";
  const GENDER_FEMALE = "female";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterJoinedAt() {
    return $this->localeModel->formatDate($this->joined);
  }
  
  protected function getterLastActiveAt() {
    return $this->localeModel->formatDate($this->lastActive);
  }
  
  /**
   * @return string[]
   */
  static function getGenders() {
    return array(
      self::GENDER_MALE => "muž",
      self::GENDER_FEMALE => "žena"
    );
  }
  
  protected function getterMoneyT() {
    return $this->localeModel->money($this->money);
  }
  
  protected function setterLife($value) {
    if($value > 60) return 60;
    elseif($value < 1) return 1;
    else return $value;
  }
}
?>