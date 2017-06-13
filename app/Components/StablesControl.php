<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\MountNotFoundException,
    Nexendrie\Model\MountNotOwnedException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\CareNotNeededException,
    Nexendrie\Model\MountMaxTrainingLevelReachedException,
    Nexendrie\Model\MountInBadConditionException;

/**
 * StablesControl
 *
 * @author Jakub Konečný
 */
class StablesControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    parent::__construct();
    $this->orm = $orm;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function render(): void {
    $this->template->setFile(__DIR__ . "/stables.latte");
    $this->template->mounts = $this->orm->mounts->findByOwner($this->user->id);
    $this->template->render();
  }
  
  /**
   * @param int $mountId
   * @return void
   */
  function renderTrain(int $mountId): void {
    $this->template->setFile(__DIR__ . "/stablesTrain.latte");
    $this->template->mount = $this->orm->mounts->getById($mountId);
    $this->template->render();
  }
  
  /**
   * Increase specified mount's life
   * 
   * @param int $mountId
   * @param int $hp
   * @param int $price
   * @return void
   * @throws MountNotFoundException
   * @throws MountNotOwnedException
   * @throws InsufficientFundsException
   * @throws CareNotNeededException
   */
  protected function increaseLife(int $mountId, int $hp, int $price): void {
    $mount = $this->orm->mounts->getById($mountId);
    if(!$mount) {
      throw new MountNotFoundException;
    }
    if($mount->owner->id != $this->user->id) {
      throw new MountNotOwnedException;
    }
    if($mount->owner->money < $price) {
      throw new InsufficientFundsException;
    }
    if($mount->hp >= 100) {
      throw new CareNotNeededException;
    }
    $mount->hp += $hp;
    $mount->owner->money -= $price;
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  /**
   * @param int $mountId
   * @return void
   */
  function handleCare(int $mountId): void {
    try {
      $this->increaseLife($mountId, 3, 4);
      $message = $this->localeModel->genderMessage("Očistil(a) jsi jezdecké zvíře.");
      $this->presenter->flashMessage($message);
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOwnedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    } catch(CareNotNeededException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře nepotřebuje čištění.");
    }
    $this->presenter->redirect("default");
  }
  
  /**
   * @param int $mountId
   * @return void
   */
  function handleFeed(int $mountId): void {
    try {
      $this->increaseLife($mountId, 10, 12);
      $message = $this->localeModel->genderMessage("Nakrmil(a) jsi jezdecké zvíře.");
      $this->presenter->flashMessage($message);
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOwnedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    } catch(CareNotNeededException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře nepotřebuje krmení.");
    }
    $this->presenter->redirect("default");
  }
  
  /**
   * Train specified mount
   * 
   * @param int $mountId
   * @param string $stat
   * @return void
   * @throws MountNotFoundException
   * @throws MountNotOwnedException
   * @throws MountMaxTrainingLevelReachedException
   * @throws MountInBadConditionException
   * @throws InsufficientFundsException
   */
  protected function train(int $mountId, string $stat): void {
    $stats = ["damage", "armor"];
    if(!in_array($stat, $stats)) {
      return;
    }
    $mount = $this->orm->mounts->getById($mountId);
    if(!$mount) {
      throw new MountNotFoundException;
    } elseif($mount->owner->id != $this->user->id) {
      throw new MountNotOwnedException;
    }
    $statCap = ucfirst($stat);
    if($mount->$stat >= $mount->{"max" . $statCap}) {
      throw new MountMaxTrainingLevelReachedException;
    } elseif($mount->hp < 40) {
      throw new MountInBadConditionException;
    } elseif($mount->owner->money < $mount->{$stat . "TrainingCost"}) {
      throw new InsufficientFundsException;
    }
    $mount->owner->money -= $mount->{$stat . "TrainingCost"};
    $mount->$stat++;
    $mount->hp -= 10;
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  /**
   * @param int $mount
   * @return void
   */
  function handleTrainDamage(int $mount): void {
    try {
      $this->train($mount, "damage");
      $this->presenter->flashMessage("Trénink byl úspěšný.");
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOwnedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    } catch(MountMaxTrainingLevelReachedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře už nemůže být trénováno.");
    } catch(MountInBadConditionException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře je ve špatném stavu.");
    }
    $this->presenter->redirect("default");
  }
  
  /**
   * @param int $mount
   * @return void
   */
  function handleTrainArmor(int $mount): void {
    try {
      $this->train($mount, "armor");
      $this->presenter->flashMessage("Trénink byl úspěšný.");
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOwnedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
    } catch(InsufficientFundsException $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz.");
    } catch(MountMaxTrainingLevelReachedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře už nemůže být trénováno.");
    } catch(MountInBadConditionException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře je ve špatném stavu.");
    }
    $this->presenter->redirect("default");
  }
}
?>