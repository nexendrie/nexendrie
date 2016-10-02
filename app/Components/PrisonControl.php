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
    $this->orm = $orm;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  /**
   * @param Punishment $punishment
   * @return bool
   */
  protected function canWork(Punishment $punishment) {
    if($punishment->lastAction === NULL) return true;
    elseif(time() >$punishment->nextShift) return true;
    else return false;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/prison.latte");
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    $template->noCrime = $template->release = false;
    if($punishment === NULL) {
      $template->noCrime = true;
    } else {
      if($punishment->count >= $punishment->numberOfShifts) $template->release = true;
      $template->punishment = $punishment;
      $canWork = $this->canWork($punishment);
      $template->canWork = $canWork;
      if(!$canWork) $template->nextShift = $this->localeModel->formatDateTime($punishment->nextShift);
    }
    $template->render();
  }
  
  /**
   * @return void
   */
  function handleWork() {
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    if(!$punishment === NULL) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Nejsi uvězněná.";
      else $message = "Nejsi uvězněný.";
      $this->presenter->flashMessage($message);
    } elseif(!$this->canWork($punishment)) {
      $this->presenter->flashMessage("Ještě nemůžeš pracovat.");
    } elseif($punishment->count >= $punishment->numberOfShifts) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Už jsi odpracovala svůj trest.";
      else $message = "Už jsi odpracoval svůj trest.";
      $this->presenter->flashMessage($message);
    } else {
      $punishment->count++;
      $punishment->lastAction = $punishment->user->lastActive = time();
      $this->orm->punishments->persistAndFlush($punishment);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Úspěšně jsi zvládla směnu.";
      else $message = "Úspěšně jsi zvládl směnu.";
      $this->presenter->flashMessage($message);
    }
    $this->presenter->redirect("default");
  }
  
  /**
   * @return void
   */
  function handleRelease() {
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    $release = false;
    if(!$punishment === NULL) {
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
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Byla jsi propuštěna.";
      else $message = "Byl jsi propuštěn.";
      $this->presenter->flashMessage($message);
      $this->presenter->redirect(":Front:Homepage:");
    } else {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Ještě nemůžeš být propuštěna.";
      else $message = "Ještě nemůžeš být propuštěn.";
      $this->presenter->flashMessage($message);
      $this->presenter->redirect("default");
    }
  }
}

interface PrisonControlFactory {
  /** @return PrisonControl */
  function create();
}
?>