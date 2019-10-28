<?php
declare(strict_types=1);

namespace Nexendrie\Model;

// @codingStandardsIgnoreFile

class AccessDeniedException extends \RuntimeException {
  
}

class AuthenticationNeededException extends AccessDeniedException {
  
}

class MissingPermissionsException extends AccessDeniedException {
  
}

abstract class RecordNotFoundException extends \RuntimeException {
  
}

class ImprisonedException extends AccessDeniedException {
  
}

class InsufficientFundsException extends AccessDeniedException {
  
}

class NameInUseException extends \RuntimeException {
  
}

class InsufficientLevelException extends AccessDeniedException {
  
}

class CannotPromoteMemberException extends AccessDeniedException {
  
}

class CannotDemoteMemberException extends AccessDeniedException {
  
}

class CannotKickMemberException extends AccessDeniedException {
  
}

class AdventureNotFoundException extends RecordNotFoundException {
  
}

class AdventureNpcNotFoundException extends RecordNotFoundException {
  
}

class AlreadyOnAdventureException extends AccessDeniedException {
  
}

class InsufficientLevelForAdventureException extends InsufficientLevelException {
  
}

class NotOnAdventureException extends AccessDeniedException {
  
}

class NoEnemyRemainException extends AccessDeniedException {
  
}

class NotAllEnemiesDefeatedException extends AccessDeniedException {
  
}

class CannotDoAdventureException extends AccessDeniedException {
  
}

class AdventureNotAccessibleException extends AccessDeniedException {
  
}

class ArticleNotFoundException extends RecordNotFoundException {
  
}

class TooHighLoanException extends AccessDeniedException {
  
}

class CannotTakeMoreLoansException extends AccessDeniedException {
  
}

class NoLoanException extends AccessDeniedException {
  
}

class CastleNotFoundException extends RecordNotFoundException {
  
}

class CannotBuildCastleException extends AccessDeniedException {
  
}

class CannotBuildMoreCastlesException extends AccessDeniedException {
  
}

class CastleNameInUseException extends NameInUseException {
  
}

class CannotUpgradeCastleException extends AccessDeniedException {
  
}

class CannotRepairCastleException extends AccessDeniedException {
  
}

class EventNotFoundException extends RecordNotFoundException {
  
}

class CannotDeleteStartedEventException extends AccessDeniedException {
  
}

class GroupNotFoundException extends RecordNotFoundException {
  
}

class GuildNotFoundException extends RecordNotFoundException {
  
}

class CannotFoundGuildException extends AccessDeniedException {
  
}

class GuildNameInUseException extends NameInUseException {
  
}

class CannotJoinGuildException extends AccessDeniedException {
  
}

class CannotLeaveGuildException extends AccessDeniedException {
  
}

class CannotUpgradeGuildException extends AccessDeniedException {

}

class UserNotInYourGuildException extends AccessDeniedException {
  
}

class CannotBuyMoreHousesException extends AccessDeniedException {
  
}

class CannotBuyHouseException extends AccessDeniedException {
  
}

class CannotUpgradeHouseException extends AccessDeniedException {
  
}

class CannotRepairHouseException extends AccessDeniedException {
  
}

class CannotUpgradeBreweryException extends AccessDeniedException {
  
}

class CannotProduceBeerException extends AccessDeniedException {
  
}

class ItemNotOwnedException extends AccessDeniedException {
  
}

class ItemNotEquipableException extends AccessDeniedException {

}

class JobNotFoundException extends RecordNotFoundException {
  
}

class AlreadyWorkingException extends AccessDeniedException {
  
}

class InsufficientLevelForJobException extends InsufficientLevelException {
  
}

class InsufficientSkillLevelForJobException extends AccessDeniedException {
  
}

class NotWorkingException extends AccessDeniedException {
  
}

class CannotWorkException extends AccessDeniedException {
  
}

class JobNotFinishedException extends AccessDeniedException {
  
}

class JobMessageNotFoundException extends RecordNotFoundException {
  
}

class ShopNotFoundException extends RecordNotFoundException {
  
}

class ItemNotFoundException extends RecordNotFoundException {
  
}

class WrongShopException extends AccessDeniedException {
  
}

class ItemAlreadyWornException extends AccessDeniedException {
  
}

class ItemNotWornException extends AccessDeniedException {
  
}

class ItemNotUsableException extends AccessDeniedException {
  
}

class ItemNotDrinkableException extends ItemNotUsableException {

}

class HealingNotNeededException extends AccessDeniedException {

}

class ItemNotForSaleException extends AccessDeniedException {

}

class ItemNotUpgradableException extends AccessDeniedException {
  
}

class ItemMaxLevelReachedException extends AccessDeniedException {
  
}

class ItemSetNotFoundException extends RecordNotFoundException {
  
}

class CannotProposeMarriageException extends AccessDeniedException {
  
}

class MarriageNotFoundException extends RecordNotFoundException {
  
}

class MarriageProposalAlreadyHandledException extends AccessDeniedException {
  
}

class NotEngagedException extends AccessDeniedException {
  
}

class NotMarriedException extends AccessDeniedException {
  
}

class WeddingAlreadyHappenedException extends AccessDeniedException {
  
}

class AlreadyInDivorceException extends AccessDeniedException {
  
}

class NotInDivorceException extends AccessDeniedException {
  
}

class CannotTakeBackDivorceException extends AccessDeniedException {
  
}

class MaxIntimacyReachedException extends AccessDeniedException {
  
}

class MessageNotFoundException extends RecordNotFoundException {
  
}

class MonasteryNotFoundException extends RecordNotFoundException {
  
}

class NotInMonasteryException extends AccessDeniedException {
  
}

class CannotJoinMonasteryException extends AccessDeniedException {
  
}

class CannotPrayException extends AccessDeniedException {
  
}

class CannotLeaveMonasteryException extends AccessDeniedException {
  
}

class CannotBuildMonasteryException extends AccessDeniedException {
  
}

class MonasteryNameInUseException extends NameInUseException {
  
}

class CannotJoinOwnMonasteryException extends AccessDeniedException {
  
}

class CannotUpgradeMonasteryException extends AccessDeniedException {

}

class CannotRepairMonasteryException extends AccessDeniedException {

}

class MountNotFoundException extends RecordNotFoundException {
  
}

class MountNotOnSaleException extends AccessDeniedException {
  
}

class InsufficientLevelForMountException extends InsufficientLevelException {
  
}

class CannotBuyOwnMountException extends AccessDeniedException {
  
}

class MountNotOwnedException extends AccessDeniedException {
  
}

class CareNotNeededException extends AccessDeniedException {
  
}

class MountInBadConditionException extends AccessDeniedException {
  
}

class MountMaxTrainingLevelReachedException extends AccessDeniedException {
  
}

class OrderNotFoundException extends RecordNotFoundException {
  
}

class CannotFoundOrderException extends AccessDeniedException {
  
}

class OrderNameInUseException extends NameInUseException {
  
}

class CannotJoinOrderException extends AccessDeniedException {
  
}

class CannotLeaveOrderException extends AccessDeniedException {
  
}

class CannotUpgradeOrderException extends AccessDeniedException {

}

class UserNotInYourOrderException extends AccessDeniedException {
  
}

class PollVotingException extends \Exception {
  
}

class PollNotFoundException extends RecordNotFoundException {
  
}

class UserNotFoundException extends RecordNotFoundException {
  
}

class SkillNotFoundException extends RecordNotFoundException {
  
}

class SkillMaxLevelReachedException extends AccessDeniedException {
  
}

class MealNotFoundException extends RecordNotFoundException {
  
}

class TownNotFoundException extends RecordNotFoundException {
  
}

class TownNotOnSaleException extends AccessDeniedException {
  
}

class CannotBuyTownException extends AccessDeniedException {
  
}

class CannotBuyOwnTownException extends AccessDeniedException {
  
}

class TownNotOwnedException extends AccessDeniedException {
  
}

class InsufficientLevelForMayorException extends AccessDeniedException {
  
}

class UserDoesNotLiveInTheTownException extends AccessDeniedException {
  
}

class CannotMoveToSameTownException extends AccessDeniedException {
  
}

class CannotMoveToTownException extends AccessDeniedException {
  
}

class InsufficientLevelForFoundTownException extends InsufficientLevelException {
  
}

class CannotFoundTownException extends AccessDeniedException {
  
}

class TownNameInUseException extends NameInUseException {
  
}

class TooHighLevelException extends AccessDeniedException {
  
}

class RegistrationException extends \Exception {
  
}

class SettingsException extends \Exception {
  
}

class TooHighDepositException extends AccessDeniedException {

}

class CannotOpenMoreDepositAccountsException extends AccessDeniedException {

}

class InvalidDateException extends \RuntimeException {

}

class NoDepositAccountException extends AccessDeniedException {

}

class DepositAccountNotDueException extends AccessDeniedException {

}

class UserNotInYourMonasteryException extends AccessDeniedException {

}

class CommentNotFoundException extends RecordNotFoundException {

}

class ContentAlreadyReportedException extends AccessDeniedException {

}

class ContentReportNotFoundException extends RecordNotFoundException {

}
?>