<?php
namespace Nexendrie\Components;

use Nexendrie\Orm\Punishment;

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
      $this->presenter->flashMessage("Nejsi uvězněn.");
    } elseif(!$this->canWork($punishment)) {
      $this->presenter->flashMessage("Ještě nemůžeš pracovat.");
    } elseif($punishment->count >= $punishment->numberOfShifts) {
      $this->presenter->flashMessage("Už jsi odpracoval svůj trest.");
    } else {
      $punishment->count++;
      $punishment->lastAction = $punishment->user->lastActive = time();
      $this->orm->punishments->persistAndFlush($punishment);
      $this->presenter->flashMessage("Úspěšně jsi zvládl směnu.");
    }
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
      $this->user->identity->roles = array($punishment->user->group->singleName);
      $this->presenter->flashMessage("Byl jsi propuštěn.");
      $this->presenter->redirect(":Front:Homepage:");
    } else {
      $this->presenter->flashMessage("Ještě nemůžeš být propuštěn.");
    }
  }
}

interface PrisonControlFactory {
  /** @return PrisonControl */
  function create();
}
?>