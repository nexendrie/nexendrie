<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Town as TownEntity;
use Nexendrie\Orm\Message as MessageEntity;
use Nexendrie\Orm\Group as GroupEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Town Model
 *
 * @author Jakub Konečný
 */
final class Town {
  private int $foundingPrice;
  private int $foundingCharter;
  
  public function __construct(private readonly ORM $orm, private readonly \Nette\Security\User $user, SettingsRepository $sr) {
    $this->foundingPrice = $sr->settings["fees"]["foundTown"];
    $this->foundingCharter = $sr->settings["specialItems"]["foundTown"];
  }
  
  /**
   * Get specified town
   *
   * @throws TownNotFoundException
   */
  public function get(int $id): TownEntity {
    $town = $this->orm->towns->getById($id);
    return $town ?? throw new TownNotFoundException();
  }
  
  /**
   * Get list of all towns
   * 
   * @return TownEntity[]|ICollection
   */
  public function listOfTowns(): ICollection {
    return $this->orm->towns->findAll();
  }
  
  /**
   * Add new town
   */
  public function add(array $data): void {
    $town = new TownEntity();
    $this->orm->towns->attach($town);
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * Edit specified town
   *
   * @throws TownNotFoundException
   */
  public function edit(int $id, array $data): void {
    try {
      $town = $this->get($id);
    } catch(TownNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * @return TownEntity[]|ICollection
   */
  public function townsOnSale(): ICollection {
    return $this->orm->towns->findOnMarket();
  }
  
  /**
   * Buy specified mount
   *
   * @throws AuthenticationNeededException
   * @throws TownNotFoundException
   * @throws TownNotOnSaleException
   * @throws CannotBuyOwnTownException
   * @throws CannotBuyTownException
   * @throws InsufficientFundsException
   */
  public function buy(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $town = $this->orm->towns->getById($id);
    if($town === null) {
      throw new TownNotFoundException();
    }
    if(!$town->onMarket) {
      throw new TownNotOnSaleException();
    }
    if($town->owner->id === $this->user->id) {
      throw new CannotBuyOwnTownException();
    }
    if($this->user->identity->path !== GroupEntity::PATH_TOWER) {
      throw new CannotBuyTownException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $town->price) {
      throw new InsufficientFundsException();
    }
    $seller = $town->owner;
    $seller->money += $town->price;
    $this->orm->users->persist($seller);
    $user->money -= $town->price;
    $user->lastActive = time();
    $town->owner = $user;
    $town->onMarket = false;
    $this->orm->towns->persist($town);
    $this->orm->flush();
  }
  
  public function getMayor(int $town): ?\Nexendrie\Orm\User {
    return $this->orm->users->getTownMayor($town);
  }
  
  /**
   * Get citizens of specified town
   *
   * @return string[] id => publicname
   */
  public function getTownCitizens(int $town): array {
    return $this->orm->users->findTownCitizens($town)
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Appoint new mayor of a town
   *
   * @throws AuthenticationNeededException
   * @throws TownNotFoundException
   * @throws TownNotOwnedException
   * @throws UserNotFoundException
   * @throws UserDoesNotLiveInTheTownException
   * @throws InsufficientLevelForMayorException
   */
  public function appointMayor(int $townId, int $newMayorId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $town = $this->orm->towns->getById($townId);
    if($town === null) {
      throw new TownNotFoundException();
    } elseif($town->owner->id !== $this->user->id) {
      throw new TownNotOwnedException();
    }
    $newMayor = $this->orm->users->getById($newMayorId);
    if($newMayor === null) {
      throw new UserNotFoundException();
    } elseif($newMayor->town->id !== $townId) {
      throw new UserDoesNotLiveInTheTownException();
    }
    $newMayorRank = $newMayor->group->level;
    if(!in_array($newMayorRank, [100, 300], true)) {
      throw new InsufficientLevelForMayorException();
    }
    $oldMayor = $this->orm->users->getTownMayor($townId);
    if($oldMayor !== null) {
      /** @var GroupEntity $groupOld */
      $groupOld = $this->orm->groups->getByLevel(100);
      $oldMayor->group = $groupOld;
      $this->orm->users->persist($oldMayor);
    }
    /** @var GroupEntity $groupNew */
    $groupNew = $this->orm->groups->getByLevel(345);
    $newMayor->group = $groupNew;
    $this->orm->users->persistAndFlush($newMayor);
  }
  
  /**
   * Check whether the user can move to different town (now)
   *
   * @throws AuthenticationNeededException
   */
  public function canMove(): bool {
    $month = 60 * 60 * 24 * 31;
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path === GroupEntity::PATH_CHURCH) {
      return false;
    } elseif($user->group->path === GroupEntity::PATH_CITY && $user->group->level !== 100) {
      return false;
    } elseif($user->lastTransfer === null) {
      return true;
    } elseif($user->lastTransfer + $month > time()) {
      return false;
    } elseif($user->guild !== null && $user->guildRank !== null && $user->guildRank->id === 4) {
      return false;
    }
    return true;
  }
  
  /**
   * Move to specified town
   *
   * @throws AuthenticationNeededException
   * @throws TownNotFoundException
   * @throws CannotMoveToSameTownException
   * @throws CannotMoveToTownException
   */
  public function moveToTown(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $town = $this->orm->towns->getById($id);
    if($town === null) {
      throw new TownNotFoundException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($id === $user->town->id) {
      throw new CannotMoveToSameTownException();
    } elseif(!$this->canMove()) {
      throw new CannotMoveToTownException();
    }
    $user->town = $id;
    $user->lastTransfer = $user->lastActive = time();
    $user->guild = $user->guildRank = null;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Found new town
   *
   * @throws AuthenticationNeededException
   * @throws InsufficientLevelForFoundTownException
   * @throws InsufficientFundsException
   * @throws CannotFoundTownException
   * @throws TownNameInUseException
   */
  public function found(array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path !== GroupEntity::PATH_TOWER) {
      throw new InsufficientLevelForFoundTownException();
    }
    if($user->money < $this->foundingPrice) {
      throw new InsufficientFundsException();
    }
    $item = $this->orm->userItems->getByUserAndItem($user->id, $this->foundingCharter);
    if($item === null) {
      throw new CannotFoundTownException();
    }
    if($this->orm->towns->getByName($data["name"]) !== null) {
      throw new TownNameInUseException();
    }
    $item->amount--;
    if($item->amount < 1) {
      $this->orm->userItems->removeAndFlush($item, false);
    }
    $town = new TownEntity();
    $town->name = $data["name"];
    $town->description = $data["description"];
    $town->owner = $user;
    $town->created = time();
    $town->owner->money -= $this->foundingPrice;
    $town->price = $this->foundingPrice;
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * Get peasants from specified town
   *
   * @return string[] id => publicname
   */
  public function getTownPeasants(int $town): array {
    return $this->orm->users->findTownPeasants($town)
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Promote a peasant to citizen
   *
   * @throws AuthenticationNeededException
   * @throws UserNotFoundException
   * @throws UserDoesNotLiveInTheTownException
   * @throws TooHighLevelException
   */
  public function makeCitizen(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $citizen = $this->orm->users->getById($id);
    if($citizen === null) {
      throw new UserNotFoundException();
    }
    /** @var \Nexendrie\Orm\User $owner */
    $owner = $this->orm->users->getById($this->user->id);
    if($citizen->town->owner->id !== $owner->id) {
      throw new UserDoesNotLiveInTheTownException();
    } elseif($citizen->group->level > 50) {
      throw new TooHighLevelException();
    }
    /** @var GroupEntity $group */
    $group = $this->orm->groups->getByLevel(100);
    $citizen->group = $group;
    $message = new MessageEntity();
    $message->from = $owner;
    $message->to = $citizen;
    $message->created = time();
    $message->subject = "Povýšení";
    $message->text = "Byl(a) jsi povýšen(a) na měšťana.";
    $this->orm->users->persistAndFlush($citizen);
  }

  public function canManage(TownEntity $town): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif($town->owner->id === $this->user->id) {
      return true;
    } elseif($town->owner->id !== 0) {
      return false;
    }
    return $this->user->isAllowed("town", "manage");
  }
}
?>