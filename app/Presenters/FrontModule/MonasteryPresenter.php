<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Chat\IMonasteryChatControlFactory;
use Nexendrie\Chat\MonasteryChatControl;
use Nexendrie\Model\Locale;
use Nexendrie\Model\Monastery;
use Nexendrie\Model\MonasteryNotFoundException;
use Nexendrie\Model\NotInMonasteryException;
use Nexendrie\Model\CannotJoinMonasteryException;
use Nexendrie\Model\CannotPrayException;
use Nexendrie\Model\CannotLeaveMonasteryException;
use Nexendrie\Forms\BuildMonasteryFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Forms\MonasteryDonateFormFactory;
use Nexendrie\Forms\ManageMonasteryFormFactory;
use Nexendrie\Model\CannotJoinOwnMonasteryException;
use Nexendrie\Model\CannotUpgradeMonasteryException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\CannotRepairMonasteryException;
use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\MissingPermissionsException;
use Nexendrie\Model\UserNotFoundException;
use Nexendrie\Model\UserNotInYourMonasteryException;
use Nexendrie\Model\CannotPromoteMemberException;
use Nexendrie\Model\CannotDemoteMemberException;

/**
 * Presenter Monastery
 *
 * @author Jakub Konečný
 */
final class MonasteryPresenter extends BasePresenter {
  private int $monasteryId;
  
  public function __construct(private readonly Monastery $model, private readonly Locale $localeModel, private readonly IMonasteryChatControlFactory $chatFactory) {
    parent::__construct();
  }
  
  protected function startup(): void {
    parent::startup();
    if($this->action !== "detail" && $this->action !== "list") {
      $this->requiresLogin();
    }
  }

  protected function getChat(): ?MonasteryChatControl {
    return $this->chatFactory->create();
  }
  
  public function renderDefault(): void {
    try {
      $this->template->monastery = $this->model->getByUser();
      $this->publicCache = false;
      $this->template->canPray = $this->model->canPray();
      $this->template->canLeave = $this->model->canLeave();
      $this->template->canBuild = $this->model->canBuild();
      $this->template->canManage = $this->model->canManage();
      $this->template->prayerLife = $this->model->prayerLife();
    } catch(NotInMonasteryException) {
      $this->flashMessage("Nejsi v klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  public function renderList(): void {
    $this->template->monasteries = $this->model->listOfMonasteries();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderDetail(int $id): void {
    try {
      $this->template->monastery = $this->model->get($id);
    } catch(MonasteryNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionBuild(): void {
    if(!$this->model->canBuild()) {
      $this->flashMessage("Nemůžeš postavit klášter.");
      $this->redirect("Homepage:");
    }
    $this->template->buildingPrice = $this->sr->settings["fees"]["buildMonastery"];
  }
  
  protected function createComponentBuildMonasteryForm(BuildMonasteryFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Klášter založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  public function actionChat(): void {
    try {
      $this->model->getByUser();
    } catch(NotInMonasteryException $e) {
      $this->flashMessage("Nejsi v klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionJoin(int $id): void {
    try {
      $this->model->join($id);
      /** @var \Nexendrie\Model\Authenticator $authenticator */
      $authenticator = $this->user->authenticator;
      $authenticator->user = $this->user;
      $authenticator->refreshIdentity();
      $message = $this->localeModel->genderMessage("Vstoupil(a) jsi do kláštera");
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinMonasteryException) {
      $this->flashMessage("Nemůžeš vstoupit do kláštera.");
      $this->redirect("Homepage:");
    } catch(MonasteryNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    } catch(CannotJoinOwnMonasteryException) {
      $this->flashMessage("Už jsi v tomto klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  public function actionLeave(): void {
    try {
      $this->model->leave();
      /** @var \Nexendrie\Model\Authenticator $authenticator */
      $authenticator = $this->user->authenticator;
      $authenticator->user = $this->user;
      $authenticator->refreshIdentity();
      $message = $this->localeModel->genderMessage("Vystoupil(a) jsi z kláštera");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveMonasteryException) {
      $this->flashMessage("Nemůžeš vystoupit z kláštera.");
      $this->redirect("default");
    }
  }
  
  public function actionPray(): void {
    try {
      $this->model->pray();
      $this->flashMessage("Modlitba ti přidala 5 životů.");
      $this->redirect("default");
    } catch(CannotPrayException) {
      $this->flashMessage("Nemůžeš se modlit (právě teď).");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentMonasteryDonateForm(MonasteryDonateFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Příspěvek proveden.");
    };
    return $form;
  }
  
  public function actionManage(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat klášter.");
      $this->redirect("Homepage:");
    }
    $this->template->monastery = $monastery = $this->model->getByUser();
    $this->monasteryId = $monastery->id;
    $this->template->canUpgrade = $this->model->canUpgrade();
    $this->template->canUpgradeLibrary = $this->model->canUpgradeLibrary();
    $ranks = $this->model->getChurchGroupIds();
    $this->template->firstRank = $ranks[0];
    end($ranks);
    $this->template->lastRank = current($ranks);
  }
  
  protected function createComponentManageMonasteryForm(ManageMonasteryFormFactory $factory): Form {
    $form = $factory->create($this->monasteryId);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  public function handleUpgrade(): void {
    try {
      $this->model->upgrade();
      $this->flashMessage("Klášter vylepšen.");
      $this->redirect("manage");
    } catch(CannotUpgradeMonasteryException) {
      $this->flashMessage("Nemůžeš vylepšit klášter.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }

  public function handleUpgradeLibrary(): void {
    try {
      $this->model->upgradeLibrary();
    } catch(CannotUpgradeMonasteryException) {
      $this->flashMessage("Nemůžeš vylepšit klášterní knihovnu.");
    } catch(InsufficientFundsException) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
  
  public function handleRepair(): void {
    try {
      $this->model->repair();
      $this->flashMessage("Klášter opraven.");
      $this->redirect("manage");
    } catch(CannotRepairMonasteryException) {
      $this->flashMessage("Nemůžeš opravit klášter.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }

  public function handlePromote(int $user): void {
    try {
      $this->model->promote($user);
      $this->flashMessage("Povýšen(a).");
      $this->redirect("manage");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourMonasteryException) {
      $this->flashMessage("Uživatel není ve tvém klášteru.");
      $this->redirect("Homepage:");
    } catch(CannotPromoteMemberException) {
      $this->flashMessage("Uživatel nemůže být povýšen.");
      $this->redirect("manage");
    }
  }

  public function handleDemote(int $user): void {
    try {
      $this->model->demote($user);
      $this->flashMessage("Degradován(a).");
      $this->redirect("manage");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourMonasteryException) {
      $this->flashMessage("Uživatel není ve tvém klášteru.");
      $this->redirect("Homepage:");
    } catch(CannotDemoteMemberException) {
      $this->flashMessage("Uživatel nemůže být degradován.");
      $this->redirect("manage");
    }
  }

  protected function getDataModifiedTime(): int {
    if(isset($this->template->monastery)) {
      return($this->template->monastery->updated);
    }
    if(isset($this->template->monasteries)) {
      $time = 0;
      /** @var \Nexendrie\Orm\Monastery $monastery */
      foreach($this->template->monasteries as $monastery) {
        $time = max($time, $monastery->updated);
      }
      return $time;
    }
    return 0;
  }
}
?>