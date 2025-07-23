<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Orm\Punishment;
use Nexendrie\Orm\User as UserEntity;

/**
 * Prison Control
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
final class PrisonControl extends \Nette\Application\UI\Control {
  protected \Nexendrie\Orm\Model $orm;
  protected \Nexendrie\Model\Locale $localeModel;
  protected \Nette\Security\User $user;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  protected function canWork(Punishment $punishment): bool {
    return $punishment->lastAction === null || time() > $punishment->nextShift;
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/prison.latte");
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    $this->template->noCrime = $this->template->release = false;
    if($punishment === null) {
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
  
  public function handleWork(): void {
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    if($punishment === null) {
      $message = $this->localeModel->genderMessage("Nejsi uvězněn(ý|á).");
      $this->presenter->flashMessage($message);
    } elseif(!$this->canWork($punishment)) {
      $this->presenter->flashMessage("Ještě nemůžeš pracovat.");
    } elseif($punishment->count >= $punishment->numberOfShifts) {
      $message = $this->localeModel->genderMessage("Už jsi odpracoval(a) svůj trest.");
      $this->presenter->flashMessage($message);
    } else {
      $punishment->count++;
      $punishment->lastAction = $punishment->user->lastActive = time();
      $this->orm->punishments->persistAndFlush($punishment);
      $message = $this->localeModel->genderMessage("Úspěšně jsi zvládl(a) směnu.");
      $this->presenter->flashMessage($message);
    }
    $this->presenter->redirect("default");
  }
  
  public function handleRelease(): void {
    $punishment = $this->orm->punishments->getActivePunishment($this->user->id);
    $release = false;
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($punishment === null) {
      $release = true;
      $user->lastActive = time();
      $this->orm->users->persistAndFlush($user);
    } elseif($punishment->count >= $punishment->numberOfShifts) {
      $release = true;
      $punishment->user->lastActive = $punishment->released = time();
      $this->orm->punishments->persistAndFlush($punishment);
    }
    if($release) {
      $this->user->identity->banned = false;
      $this->user->identity->roles = [$user->group->singleName];
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