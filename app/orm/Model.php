<?php
namespace Nexendrie\Orm;

/**
 * Orm Model
 *
 * @author Jakub Konečný
 * @property-read ShopsRepository $shops
 * @property-read ItemsRepository $items
 * @property-read GroupsRepository $groups
 * @property-read UsersRepository $users
 * @property-read CommentsRepository $comments
 * @property-read NewsRepository $news
 * @property-read PollsRepository $polls
 * @property-read MessagesRepository $messages
 * @property-read PollVotesRepository $pollVotes
 * @property-read UserItemsRepository $userItems
 * @property-read PermissionsRepository $permissions
 */
class Model extends \Nextras\Orm\Model\Model {
  
}
?>