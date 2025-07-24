<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Castle as CastleEntity;
use Nexendrie\Orm\Group as GroupEntity;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\UserExpense;
use Nextras\Orm\Collection\ICollection;

/**
 * Castle Model
 *
 * @author Jakub Konečný
 */
final class Castle {
  private int $buildingPrice;
  
  public function __construct(private readonly ORM $orm, private readonly \Nette\Security\User $user, SettingsRepository $sr) {
    $this->buildingPrice = $sr->settings["fees"]["buildCastle"];
  }
  
  /**
   * Get list of all castles
   * 
   * @return CastleEntity[]|ICollection
   */
  public function listOfCastles(): ICollection {
    return $this->orm->castles->findAll();
  }
  
  /**
   * Get details of specified castle
   *
   * @throws CastleNotFoundException
   */
  public function getCastle(int $id): CastleEntity {
    $castle = $this->orm->castles->getById($id);
    return $castle ?? throw new CastleNotFoundException();
  }
  
  /**
   * Check whether a name can be used
   */
  private function checkNameAvailability(string $name, int $id = null): bool {
    $castle = $this->orm->castles->getByName($name);
    return $castle === null || $castle->id === $id;
  }
  
  /**
   * Edit specified castle
   *
   * @throws CastleNotFoundException
   * @throws CastleNameInUseException
   */
  public function editCastle(int $id, array $data): void {
    try {
      $castle = $this->getCastle($id);
    } catch(CastleNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      if($key === "name" && !$this->checkNameAvailability($value, $id)) {
        throw new CastleNameInUseException();
      }
      $castle->$key = $value;
    }
    $this->orm->castles->persistAndFlush($castle);
  }
  
  /**
   * Build castle
   *
   * @throws AuthenticationNeededException
   * @throws CannotBuildCastleException
   * @throws CannotBuildMoreCastlesException
   * @throws CastleNameInUseException
   * @throws InsufficientFundsException
   */
  public function build(array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path !== GroupEntity::PATH_TOWER) {
      throw new CannotBuildCastleException();
    } elseif($this->getUserCastle() !== null) {
      throw new CannotBuildMoreCastlesException();
    } elseif(!$this->checkNameAvailability($data["name"])) {
      throw new CastleNameInUseException();
    } elseif($user->money < $this->buildingPrice) {
      throw new InsufficientFundsException();
    }
    $castle = new CastleEntity();
    $castle->name = $data["name"];
    $castle->description = $data["description"];
    $castle->owner = $user;
    $castle->owner->lastActive = time();
    $castle->owner->money -= $this->buildingPrice;
    $this->orm->castles->persistAndFlush($castle);
  }
  
  /**
   * Get specified user's castle
   */
  public function getUserCastle(int $user = null): ?CastleEntity {
    return $this->orm->castles->getByOwner($user ?? $this->user->id);
  }
  
  /**
   * Check whether the user can upgrade castle
   *
   * @throws AuthenticationNeededException
   */
  public function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $castle = $this->getUserCastle();
    if($castle === null) {
      return false;
    }
    return ($castle->level < CastleEntity::MAX_LEVEL);
  }
  
  /**
   * Upgrade castle
   *
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeCastleException
   * @throws InsufficientFundsException
   */
  public function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canUpgrade()) {
      throw new CannotUpgradeCastleException();
    }
    /** @var CastleEntity $castle */
    $castle = $this->getUserCastle();
    if($castle->owner->money < $castle->upgradePrice) {
      throw new InsufficientFundsException();
    }
    $castle->owner->money -= $castle->upgradePrice;
    $castle->level++;
    $this->orm->castles->persistAndFlush($castle);
  }
  
  /**
   * Check whether the user can repair castle
   *
   * @throws AuthenticationNeededException
   */
  public function canRepair(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $castle = $this->getUserCastle();
    return $castle !== null && $castle->hp < 100;
  }
  
  /**
   * Repair castle
   *
   * @throws AuthenticationNeededException
   * @throws CannotRepairCastleException
   * @throws InsufficientFundsException
   */
  public function repair(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canRepair()) {
      throw new CannotRepairCastleException();
    }
    /** @var CastleEntity $castle */
    $castle = $this->getUserCastle();
    $price = $castle->repairPrice;
    if($castle->owner->money < $price) {
      throw new InsufficientFundsException();
    }
    $castle->owner->money -= $price;
    $castle->hp = 100;
    $expense = new UserExpense();
    $expense->amount = $price;
    $expense->category = UserExpense::CATEGORY_CASTLE_MAINTENANCE;
    $expense->user = $castle->owner;
    $castle->owner->expenses->add($expense);
    $this->orm->castles->persistAndFlush($castle);
  }
}
?>