<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Orm\Punishment,
    Nexendrie\Orm\User as UserEntity;

/**
 * Prison Control
 *
 * @author Jakub Konečný
 */
class PrisonControl extends \Nette\Application\UI\Control {
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
   * @param Punishment $punishment
   * @return bool
   */
  protected function canWork(Punishment $punishment): bool {
    if(is_null($punishment->lastAction)) {
      return true;
    } elseif(time() >$punishment->nextShift) {
      return true;
    }
    return false;
  }
  
  /**
   * @return void
   */
  function render(): void {
    $this->template->setFile(__DIR__ . "/prison.latte");
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    $this->template->noCrime = $this->template->release = false;
    if(is_null($punishment)) {
      $this->template->noCrime = true;
    } else {
      if($punishment->count >= $punishment->numberOfShifts) {
        $this->template->release = true;
      }
      $this->template->punishment = $punishment;
      $canWork = $this->canWork($punishment);
      $this->template->canWork = $canWork;
      if(!$canWork) {
        $this->template->nextShift = $this->localeModel->formatDateTime($punishment->nextShift);
      }
    }
    $this->template->render();
  }
  
  /**
   * @return void
   */
  function handleWork(): void {
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    if(is_null($punishment)) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Nejsi uvězněná.";
      } else {
        $message = "Nejsi uvězněný.";
      }
      $this->presenter->flashMessage($message);
    } elseif(!$this->canWork($punishment)) {
      $this->presenter->flashMessage("Ještě nemůžeš pracovat.");
    } elseif($punishment->count >= $punishment->numberOfShifts) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Už jsi odpracovala svůj trest.";
      } else {
        $message = "Už jsi odpracoval svůj trest.";
      }
      $this->presenter->flashMessage($message);
    } else {
      $punishment->count++;
      $punishment->lastAction = $punishment->user->lastActive = time();
      $this->orm->punishments->persistAndFlush($punishment);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Úspěšně jsi zvládla směnu.";
      } else {
        $message = "Úspěšně jsi zvládl směnu.";
      }
      $this->presenter->flashMessage($message);
    }
    $this->presenter->redirect("default");
  }
  
  /**
   * @return void
   */
  function handleRelease(): void {
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    $release = false;
    if(is_null($punishment)) {
      $release = true;
      $user = $this->orm->users->getById($this->user->id);
      $user->banned = false;
      $user->lastActive = time();
      $this->orm->users->persistAndFlush($user);
    } elseif($punishment->count >= $punishment->numberOfShifts) {
      $release = true;
      $punishment->user->lastActive = $punishment->released = time();
      $punishment->user->banned = false;
      $this->orm->punishments->persistAndFlush($punishment);
    }
    if($release) {
      $this->user->identity->banned = false;
      $this->user->identity->roles = [$punishment->user->group->singleName];
      $message = $this->localeModel->genderMessage("Byl(a) jsi propuštěn(a).");
      $this->presenter->flashMessage($message);
      $this->presenter->redirect(":Front:Homepage:");
    }
    $message = $this->localeModel->genderMessage("Ještě nemůžeš být propuštěn(a).");
    $this->presenter->flashMessage($message);
    $this->presenter->redirect("default");
  }
}
?>