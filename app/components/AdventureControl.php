<?php
namespace Nexendrie\Components;

use Nexendrie\Model\AlreadyOnAdventureException,
    Nexendrie\Model\AdventureNotFoundException,
    Nexendrie\Model\InsufficientLevelForAdventureException,
    Nexendrie\Model\MountNotFoundException,
    Nexendrie\Model\MountNotOwnedException,
    Nexendrie\Model\MountInBadConditionException,
    Nexendrie\Model\NotOnAdventureException,
    Nexendrie\Model\NoEnemyRemainException,
    Nexendrie\Model\NotAllEnemiesDefeateException;

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
  function renderMounts($adventure) {
    $template = $this->template;
    $template->setFile(__DIR__ . "/adventureMounts.latte");
    $template->mounts = $this->model->findGoodMounts();
    $template->adventure = $adventure;
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
  function handleStart($adventure, $mount) {
    try {
      $this->model->startAdventure($adventure, $mount);
      $this->presenter->flashMessage("Vydal jsi se na dobrodružství.");
      $this->presenter->redirect("Adventure:");
    } catch(AlreadyOnAdventureException $e) {
      $this->presenter->flashMessage("Už jsi na dobrodružství.");
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
      flashMessage("Nejsi na dobrodružství.");
    } catch(NoEnemyRemainException $e) {
      $this->presenter->flashMessage("Porazil jsi již všechny nepřátele.");
    }
  }
  
  /**
   * @return void
   */
  function handleFinish() {
    try {
      $this->model->finishAdventure();
      $this->presenter->redirect("Homapage:");
    } catch(NotOnAdventureException $e) {
      $this->presenter->flashMessage("Nejsi na dobrodružství.");
    } catch(NotAllEnemiesDefeateException $e) {
      $this->presenter->flashMessage("Neporazil jsi všechn nepřátele.");
    }
  }
}

interface AdventureControlFactory {
  /** @return AdventureControl */
  function create();
}
?>