<?php
declare(strict_types=1);

namespace Nexendrie\Components;

use Nexendrie\Model\AlreadyOnAdventureException,
    Nexendrie\Model\AdventureNotFoundException,
    Nexendrie\Model\InsufficientLevelForAdventureException,
    Nexendrie\Model\MountNotFoundException,
    Nexendrie\Model\MountNotOwnedException,
    Nexendrie\Model\MountInBadConditionException,
    Nexendrie\Model\NotOnAdventureException,
    Nexendrie\Model\NoEnemyRemainException,
    Nexendrie\Model\NotAllEnemiesDefeateException,
    Nexendrie\Model\CannotDoAdventureException,
    Nexendrie\Model\AdventureNotAccessibleException,
    Nexendrie\Orm\User as UserEntity;

/**
 * AdventureControl
 *
 * @author Jakub Konečný
 */
class AdventureControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Adventure */
  protected $model;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Adventure $model, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->user = $user;
  }
  
  /**
   * @return void
   */
  function renderList() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/adventureList.latte");
    $template->adventures = $this->model->findAvailableAdventures();
    $template->render();
  }
  
  /**
   * @param int $adventure
   * @return void
   */
  function renderMounts(int $adventure) {
    $template = $this->template;
    $template->setFile(__DIR__ . "/adventureMounts.latte");
    $template->mounts = $this->model->findGoodMounts();
    $template->adventure = $adventure;
    $template->render();
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/adventure.latte");
    $template->adventure = $adventure = $this->model->getCurrentAdventure();
    $template->nextEnemy = $this->model->getNextNpc($adventure);
    $template->render();
  }
  
  /**
   * @param int $adventure
   * @param int $mount
   * @return void
   */
  function handleStart(int $adventure, int $mount) {
    try {
      $this->model->startAdventure($adventure, $mount);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Vydala jsi se na dobrodružství.";
      } else {
        $message = "Vydal jsi se na dobrodružství.";
      }
      $this->presenter->flashMessage($message);
      $this->presenter->redirect("Adventure:");
    } catch(AlreadyOnAdventureException $e) {
      $this->presenter->flashMessage("Už jsi na dobrodružství.");
    } catch(CannotDoAdventureException $e) {
      $this->presenter->flashMessage("Musíš počkat před dalším dobrodružstvím.");
    } catch(AdventureNotFoundException $e) {
      $this->presenter->flashMessage("Dobrodružství nenalezeno.");
    } catch(InsufficientLevelForAdventureException $e) {
      $this->presenter->flashMessage("Nemáš dostatečnou úroveň pro toto dobrodružství.");
    } catch(MountNotFoundException $e) {
      $this->presenter->flashMessage("Jezdecké zvíře nenalezeno.");
    } catch(MountNotOwnedException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře ti nepatří.");
    } catch(MountInBadConditionException $e) {
      $this->presenter->flashMessage("Dané jezdecké zvíře je ve špatném stavu.");
    } catch(AdventureNotAccessibleException $e) {
      $this->presenter->flashMessage("Vybrané dobrodružství není dostupné.");
    }
  }
  
  /**
   * @return void
   */
  function handleFight() {
    try {
      $result = $this->model->fight();
      $this->template->message = $result["message"];
    } catch(NotOnAdventureException $e) {
      $this->presenter->flashMessage("Nejsi na dobrodružství.");
    } catch(NoEnemyRemainException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Porazila jsi již všechny nepřátele.";
      else $message = "Porazil jsi již všechny nepřátele.";
      $this->presenter->flashMessage($message);
    }
  }
  
  /**
   * @return void
   */
  function handleFinish() {
    try {
      $this->model->finishAdventure();
      $this->presenter->redirect("Homepage:");
    } catch(NotOnAdventureException $e) {
      $this->presenter->flashMessage("Nejsi na dobrodružství.");
    } catch(NotAllEnemiesDefeateException $e) {
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Neporazila jsi všechny nepřátele.";
      else $message = "Neporazil jsi všechny nepřátele.";
      $this->presenter->flashMessage($message);
    }
  }
}

interface AdventureControlFactory {
  /** @return AdventureControl */
  function create();
}
?>