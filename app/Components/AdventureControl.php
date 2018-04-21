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
    Nexendrie\Model\NotAllEnemiesDefeatedException,
    Nexendrie\Model\CannotDoAdventureException,
    Nexendrie\Model\AdventureNotAccessibleException;

/**
 * AdventureControl
 *
 * @author Jakub Konečný
 * @property-read \Nette\Bridges\ApplicationLatte\Template $template
 */
class AdventureControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Model\Adventure */
  protected $model;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nette\Security\User */
  protected $user;
  
  public function __construct(\Nexendrie\Model\Adventure $model, \Nexendrie\Model\Locale $localeModel, \Nette\Security\User $user) {
    parent::__construct();
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->user = $user;
  }
  
  public function renderList(): void {
    $this->template->setFile(__DIR__ . "/adventureList.latte");
    $this->template->adventures = $this->model->findAvailableAdventures();
    $this->template->render();
  }
  
  public function renderMounts(int $adventure): void {
    $this->template->setFile(__DIR__ . "/adventureMounts.latte");
    $this->template->mounts = $this->model->findGoodMounts();
    $this->template->adventure = $adventure;
    $this->template->render();
  }
  
  public function render(): void {
    $this->template->setFile(__DIR__ . "/adventure.latte");
    $this->template->adventure = $adventure = $this->model->getCurrentAdventure();
    $this->template->nextEnemy = $adventure->nextEnemy;
    $this->template->render();
  }
  
  public function handleStart(int $adventure, int $mount): void {
    try {
      $this->model->startAdventure($adventure, $mount);
      $message = $this->localeModel->genderMessage("Vydal(a) jsi se na dobrodružství.");
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
  
  public function handleFight(): void {
    try {
      $result = $this->model->fight();
      $this->template->message = $result["message"];
    } catch(NotOnAdventureException $e) {
      $this->presenter->flashMessage("Nejsi na dobrodružství.");
    } catch(NoEnemyRemainException $e) {
      $message = $this->localeModel->genderMessage("Porazil(a) jsi již všechny nepřátele.");
      $this->presenter->flashMessage($message);
    }
  }
  
  public function handleFinish(): void {
    try {
      $this->model->finishAdventure();
      $this->presenter->redirect("Homepage:");
    } catch(NotOnAdventureException $e) {
      $this->presenter->flashMessage("Nejsi na dobrodružství.");
    } catch(NotAllEnemiesDefeatedException $e) {
      $message = $this->localeModel->genderMessage("Neporazil(a) jsi již všechny nepřátele.");
      $this->presenter->flashMessage($message);
    }
  }
}
?>