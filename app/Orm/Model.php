<?php
declare(strict_types=1);

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
 * @property-read ArticlesRepository $articles
 * @property-read PollsRepository $polls
 * @property-read MessagesRepository $messages
 * @property-read PollVotesRepository $pollVotes
 * @property-read UserItemsRepository $userItems
 * @property-read PermissionsRepository $permissions
 * @property-read JobsRepository $jobs
 * @property-read UserJobsRepository $userJobs
 * @property-read JobMessagesRepository $jobMessages
 * @property-read TownsRepository $towns
 * @property-read MountsRepository $mounts
 * @property-read MountTypesRepository $mountTypes
 * @property-read SkillsRepository $skills
 * @property-read UserSkillsRepository $userSkills
 * @property-read PunishmentsRepository $punishments
 * @property-read LoansRepository $loans
 * @property-read MealsRepository $meals
 * @property-read AdventuresRepository $adventures
 * @property-read AdventureNpcsRepository $adventureNpcs
 * @property-read UserAdventuresRepository $userAdventures
 * @property-read MonasteriesRepository $monasteries
 * @property-read MonasteryDonationsRepository $monasteryDonations
 * @property-read CastlesRepository $castles
 * @property-read EventsRepository $events
 * @property-read HousesRepository $houses
 * @property-read BeerProductionRepository $beerProduction
 * @property-read GuildsRepository $guilds
 * @property-read GuildRanksRepository $guildRanks
 * @property-read OrdersRepository $orders
 * @property-read OrderRanksRepository $orderRanks
 * @property-read ItemSetsRepository  $itemSets
 * @property-read MarriagesRepository $marriages
 * @property-read ElectionsRepository $elections
 * @property-read ElectionResultsRepository $electionResults
 */
class Model extends \Nextras\Orm\Model\Model {
  
}
?>