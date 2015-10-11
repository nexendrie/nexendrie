<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;


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
 * @property int|NULL $lastActive
 * @property Group $group {m:1 Group::$members}
 * @property int $infomails {default 0}
 * @property string $style {default default}
 * @property int $banned {default 0}
 * @property int $money {default 2}
 * @property-read string $moneyT {virtual}
 * @property OneHasMany|Comment[] $comments {1:m Comment::$author}
 * @property OneHasMany|News[] $news {1:m News::$author}
 * @property OneHasMany|Poll[] $polls {1:m Poll::$author}
 * @property OneHasMany|Message[] $sentMessages {1:m Message::$from}
 * @property OneHasMany|Message[] $receivedMessages {1:m Message::$to}
 * @property OneHasMany|PollVote[] $pollVotes {1:m PollVote::$user}
 * @property OneHasMany|UserItem[] $items {1:m UserItem::$user}
 */
class User extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterJoinedAt() {
    return $this->localeModel->formatDate($this->joined);
  }
  
  function getterMoneyT() {
    return $this->localeModel->money($this->money);
  }
}
?>