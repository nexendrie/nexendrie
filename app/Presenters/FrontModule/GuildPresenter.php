<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\FoundGuildFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\GuildNotFoundException,
    Nexendrie\Model\CannotLeaveGuildException,
    Nexendrie\Model\CannotJoinGuildException,
    Nexendrie\Forms\ManageGuildFormFactory,
    Nexendrie\Model\CannotUpgradeGuildException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\AuthenticationNeededException,
    Nexendrie\Model\MissingPermissionsException,
    Nexendrie\Model\UserNotFoundException,
    Nexendrie\Model\UserNotInYourGuildException,
    Nexendrie\Model\CannotPromoteMemberException,
    Nexendrie\Model\CannotDemoteMemberException,
    Nexendrie\Model\CannotKickMemberException;

/**
 * Presenter Guild
 *
 * @author Jakub Konečný
 */
class GuildPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Guild @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  
  protected function startup(): void {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") {
      $this->requiresLogin();
    }
  }
  
  public function renderDefault(): void {
    $guild = $this->model->getUserGuild();
    if(!$guild) {
      $this->flashMessage("Nejsi v cechu.");
      $this->redirect("Homepage:");
    }
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
    } catch(GuildNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  public function actionFound(): void {
    if(!$this->model->canFound()) {
      $this->flashMessage("Nemůžeš založit cech.");
      $this->redirect("Homepage:");
    }
    $this->template->foundingPrice = $this->localeModel->money($this->model->foundingPrice);
  }
  
  protected function createComponentFoundGuildForm(FoundGuildFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Cech založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function actionJoin(int $id): void {
    try {
      $this->model->join($id);
      $message = $this->localeModel->genderMessage("Vstoupil(a) jsi do cechu.");
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinGuildException $e) {
      $this->flashMessage("Nemůžeš vstoupit do cechu.");
      $this->redirect("Homepage:");
    } catch(GuildNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  public function actionLeave(): void {
    try {
      $this->model->leave();
      $message = $this->localeModel->genderMessage("Opustil(a) jsi cechu.");
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveGuildException $e) {
      $this->flashMessage("Nemůžeš opustit cech.");
      $this->redirect("Homepage:");
    }
  }
  
  public function actionManage(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat cech.");
      $this->redirect("Homepage:");
    }
    $this->template->guild =  $this->model->getUserGuild();
      $this->template->canUpgrade = $this->model->canUpgrade();
  }
  
  protected function createComponentManageGuildForm(ManageGuildFormFactory $factory): Form {
    $form = $factory->create($this->model->getUserGuild()->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  public function handleUpgrade(): void {
    try {
      $this->model->upgrade();
      $this->flashMessage("Cech vylepšen.");
      $this->redirect("manage");
    } catch(CannotUpgradeGuildException $e) {
      $this->flashMessage("Nemůžeš vylepšit cech.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
  
  public function actionMembers(): void {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat cech.");
      $this->redirect("Homepage:");
    }
    $guild = $this->model->getUserGuild()->id;
    $this->template->members = $this->model->getMembers($guild);
    $this->template->maxRank = $this->model->maxRank;
  }
  
  public function handlePromote(int $user): void {
    try {
      $this->model->promote($user);
      $this->flashMessage("Povýšen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException $e) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException $e) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourGuildException $e) {
      $this->flashMessage("Uživatel není ve tvém cechu.");
      $this->redirect("Homepage:");
    } catch(CannotPromoteMemberException $e) {
      $this->flashMessage("Uživatel nemůže být povýšen.");
      $this->redirect("members");
    }
  }
  
  public function handleDemote(int $user): void {
    try {
      $this->model->demote($user);
      $this->flashMessage("Degradován(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException $e) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException $e) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourGuildException $e) {
      $this->flashMessage("Uživatel není ve tvém cechu.");
      $this->redirect("Homepage:");
    } catch(CannotDemoteMemberException $e) {
      $this->flashMessage("Uživatel nemůže být degradován.");
      $this->redirect("members");
    }
  }
  
  public function handleKick(int $user) {
    try {
      $this->model->kick($user);
      $this->flashMessage("Vyloučen(a)");
      $this->redirect("members");
    } catch(AuthenticationNeededException $e) {
      $this->flashMessage("K této akci musíš být přihlášený.");
      $this->redirect("User:login");
    } catch(MissingPermissionsException $e) {
      $this->flashMessage("K této akci nemáš práva.");
      $this->redirect("Homepage:");
    } catch(UserNotFoundException $e) {
      $this->flashMessage("Uživatel nenalezen.");
      $this->redirect("Homepage:");
    } catch(UserNotInYourGuildException $e) {
      $this->flashMessage("Uživatel není ve tvém cechu.");
      $this->redirect("Homepage:");
    } catch(CannotKickMemberException $e) {
      $this->flashMessage("Uživatel nemůže být vyloučen.");
      $this->redirect("members");
    }
  }
}
?>