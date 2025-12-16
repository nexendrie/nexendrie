<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Chat\GuildChatControl;
use Nexendrie\Chat\IGuildChatControlFactory;
use Nexendrie\Forms\FoundGuildFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\Guild;
use Nexendrie\Model\GuildNotFoundException;
use Nexendrie\Model\CannotLeaveGuildException;
use Nexendrie\Model\CannotJoinGuildException;
use Nexendrie\Forms\ManageGuildFormFactory;
use Nexendrie\Model\CannotUpgradeGuildException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\AuthenticationNeededException;
use Nexendrie\Model\Locale;
use Nexendrie\Model\MissingPermissionsException;
use Nexendrie\Model\UserNotFoundException;
use Nexendrie\Model\UserNotInYourGuildException;
use Nexendrie\Model\CannotPromoteMemberException;
use Nexendrie\Model\CannotDemoteMemberException;
use Nexendrie\Model\CannotKickMemberException;

/**
 * Presenter Guild
 *
 * @author Jakub Konečný
 */
final class GuildPresenter extends BasePresenter {
  public function __construct(private readonly Guild $model, private readonly Locale $localeModel, private readonly IGuildChatControlFactory $chatFactory) {
    parent::__construct();
  }
  
  protected function startup(): void {
    parent::startup();
    if($this->action !== "detail" && $this->action !== "list") {
      $this->requiresLogin();
    }
  }

  protected function getChat(): GuildChatControl {
    return $this->chatFactory->create();
  }
  
  public function renderDefault(): void {
    $guild = $this->model->getUserGuild();
    if($guild === null) {
      $this->flashMessage("Nejsi v cechu.");
      $this->redirect("Homepage:");
    }
    $this->publicCache = false;
    $this->template->guild = $guild;
    $this->template->canLeave = $this->model->canLeave();
    $this->template->canManage = $this->model->canManage();
  }
  
  public function renderList(): void {
    $this->template->guilds = $this->model->listOfGuilds();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderDetail(int $id): void {
    try {
      $this->template->guild = $this->model->getGuild($id);
    } catch(GuildNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionFound(): void {
    if(!$this->model->canFound()) {
      $this->flashMessage("Nemůžeš založit cech.");
      $this->redirect("Homepage:");
    }
    $this->template->foundingPrice = $this->sr->settings["fees"]["foundGuild"];
  }
  
  protected function createComponentFoundGuildForm(FoundGuildFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Cech založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionJoin(int $id): never {
    try {
      $this->model->join($id);
      $message = $this->localeModel->genderMessage("Vstoupil(a) jsi do cechu.");
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinGuildException) {
      $this->flashMessage("Nemůžeš vstoupit do cechu.");
      $this->redirect("Homepage:");
    } catch(GuildNotFoundException) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionLeave(): never {
    try {
      $this->model->leave();
      $message = $this->localeModel->genderMessage("Opustil(a) jsi cechu.");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveGuildException) {
      $this->flashMessage("Nemůžeš opustit cech.");
      $this->redirect("Homepage:");
    }
  }
  
  public function actionManage(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat cech.");
      $this->redirect("Homepage:");
    }
    $this->template->guild = $this->model->getUserGuild();
    $this->template->canUpgrade = $this->model->canUpgrade();
  }
  
  protected function createComponentManageGuildForm(ManageGuildFormFactory $factory): Form {
    /** @var \Nexendrie\Orm\Guild $guild */
    $guild = $this->model->getUserGuild();
    $form = $factory->create($guild->id);
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  public function handleUpgrade(): never {
    try {
      $this->model->upgrade();
      $this->flashMessage("Cech vylepšen.");
      $this->redirect("manage");
    } catch(CannotUpgradeGuildException) {
      $this->flashMessage("Nemůžeš vylepšit cech.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
  
  public function actionMembers(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat cech.");
      $this->redirect("Homepage:");
    }
    /** @var \Nexendrie\Orm\Guild $guild */
    $guild = $this->model->getUserGuild();
    $this->template->members = $this->model->getMembers($guild->id);
    $this->template->maxRank = $this->model->maxRank;
  }
  
  public function handlePromote(int $user): never {
    try {
      $this->model->promote($user);
      $this->flashMessage("Povýšen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourGuildException) {
      $this->flashMessage("Uživatel není ve tvém cechu.");
      $this->redirect("Homepage:");
    } catch(CannotPromoteMemberException) {
      $this->flashMessage("Uživatel nemůže být povýšen.");
      $this->redirect("members");
    }
  }
  
  public function handleDemote(int $user): never {
    try {
      $this->model->demote($user);
      $this->flashMessage("Degradován(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourGuildException) {
      $this->flashMessage("Uživatel není ve tvém cechu.");
      $this->redirect("Homepage:");
    } catch(CannotDemoteMemberException) {
      $this->flashMessage("Uživatel nemůže být degradován.");
      $this->redirect("members");
    }
  }
  
  public function handleKick(int $user): never {
    try {
      $this->model->kick($user);
      $this->flashMessage("Vyloučen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourGuildException) {
      $this->flashMessage("Uživatel není ve tvém cechu.");
      $this->redirect("Homepage:");
    } catch(CannotKickMemberException) {
      $this->flashMessage("Uživatel nemůže být vyloučen.");
      $this->redirect("members");
    }
  }
  
  public function actionChat(): void {
    $guild = $this->model->getUserGuild();
    if($guild === null) {
      $this->flashMessage("Nejsi v cechu.");
      $this->redirect("Homepage:");
    }
  }

  protected function getDataModifiedTime(): int {
    if(isset($this->template->guild)) {
      return($this->template->guild->updated);
    }
    if(isset($this->template->guilds)) {
      $time = 0;
      /** @var \Nexendrie\Orm\Guild $guild */
      foreach($this->template->guilds as $guild) {
        $time = max($time, $guild->updated);
      }
      return $time;
    }
    return 0;
  }
}
?>