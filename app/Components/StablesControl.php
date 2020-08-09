<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\MountNotFoundException;
use Nexendrie\Model\MountNotOwnedException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\CareNotNeededException;
use Nexendrie\Model\MountMaxTrainingLevelReachedException;
use Nexendrie\Model\MountInBadConditionException;
use Nexendrie\Orm\Mount as MountEntity;

/**
 * StablesControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class StablesControl extends \Nette\Application\UI\Control {
  protected \Nexendrie\Orm\Model $orm;
  protected \Nexendrie\Model\Locale $localeModel;
  protected \Nette\Security\User $user;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    parent::__construct();
    $this->orm = $orm;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/stables.latte");
    $this->template->mounts = $this->orm->mounts->findByOwner($this->user->id);
    $this->template->render();
  }
  
  public function renderTrain(int $mountId): void {
    $this->template->setFile(__DIR__ . "/stablesTrain.latte");
    $this->template->mount = $this->orm->mounts->getById($mountId);
    $this->template->render();
  }
  
  /**
   * Increase specified mount's life
   *
   * @throws MountNotFoundException
   * @throws MountNotOwnedException
   * @throws InsufficientFundsException
   * @throws CareNotNeededException
   */
  protected function increaseLife(int $id, int $hp, int $price): void {
    $mount = $this->orm->mounts->getById($id);
    if($mount === null) {
      throw new MountNotFoundException();
    }
    if($mount->owner->id !== $this->user->id) {
      throw new MountNotOwnedException();
    }
    if($mount->owner->money < $price) {
      throw new InsufficientFundsException();
    }
    if($mount->hp >= 100) {
      throw new CareNotNeededException();
    }
    $mount->hp += $hp;
    $mount->owner->money -= $price;
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  public function handleCare(int $mount): void {
    try {
      $this->increaseLife($mount, 3, 4);
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
  
  public function handleFeed(int $mount): void {
    try {
      $this->increaseLife($mount, 10, 12);
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
   * @throws MountNotFoundException
   * @throws MountNotOwnedException
   * @throws MountMaxTrainingLevelReachedException
   * @throws MountInBadConditionException
   * @throws InsufficientFundsException
   */
  protected function train(int $id, string $stat): void {
    $stats = ["damage", "armor"];
    if(!in_array($stat, $stats, true)) {
      return;
    }
    $mount = $this->orm->mounts->getById($id);
    if($mount === null) {
      throw new MountNotFoundException();
    } elseif($mount->owner->id !== $this->user->id) {
      throw new MountNotOwnedException();
    }
    $statCap = ucfirst($stat);
    if($mount->$stat >= $mount->{"max" . $statCap}) {
      throw new MountMaxTrainingLevelReachedException();
    } elseif($mount->hp < 40) {
      throw new MountInBadConditionException();
    } elseif($mount->owner->money < $mount->{$stat . "TrainingCost"}) {
      throw new InsufficientFundsException();
    }
    $mount->owner->money -= $mount->{$stat . "TrainingCost"};
    $mount->$stat++;
    $mount->hp -= MountEntity::HP_DECREASE_TRAINING;
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  public function handleTrainDamage(int $mount): void {
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
  
  public function handleTrainArmor(int $mount): void {
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