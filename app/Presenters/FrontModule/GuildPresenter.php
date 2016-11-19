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
    Nexendrie\Model\CannotKickMemberException,
    Nexendrie\Orm\User as UserEntity;

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
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") {
      $this->requiresLogin();
    }
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $guild = $this->model->getUserGuild();
    if(!$guild) {
      $this->flashMessage("Nejsi v cechu.");
      $this->redirect("Homepage:");
    }
    $this->template->guild = $guild;
    $this->template->canLeave = $this->model->canLeave();
    $this->template->canManage = $this->model->canManage();
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->guilds = $this->model->listOfGuilds();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function renderDetail(int $id) {
    try {
      $this->template->guild = $this->model->getGuild($id);
    } catch(GuildNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @return void
   */
  function actionFound() {
    if(!$this->model->canFound()) {
      $this->flashMessage("Nemůžeš založit cech.");
      $this->redirect("Homepage:");
    }
    $this->template->foundingPrice = $this->localeModel->money($this->model->foundingPrice);
  }
  
  /**
   * @param FoundGuildFormFactory $factory
   * @return Form
   */
  protected function createComponentFoundGuildForm(FoundGuildFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Cech založen.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @param int $id
   * @return void
   * @throws \Nette\Application\BadRequestException
   */
  function actionJoin(int $id) {
    try {
      $this->model->join($id);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Vstoupila jsi do cechu.";
      } else {
        $message = "Vstoupil jsi do cechu.";
      }
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinGuildException $e) {
      $this->flashMessage("Nemůžeš vstoupit do cechu.");
      $this->redirect("Homepage:");
    } catch(GuildNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @return void
   */
  function actionLeave() {
    try {
      $this->model->leave();
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Opustila jsi cech.";
      } else {
        $message = "Opustil jsi cech.";
      }
      $this->flashMessage($message);
      $this->redirect("Homepage:");
    } catch(CannotLeaveGuildException $e) {
      $this->flashMessage("Nemůžeš opustit cech.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function actionManage() {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat cech.");
      $this->redirect("Homepage:");
    } else {
      $this->template->guild =  $this->model->getUserGuild();
      $this->template->canUpgrade = $this->model->canUpgrade();
    }
  }
  
  /**
   * @param ManageGuildFormFactory $factory
   * @return Form
   */
  protected function createComponentManageGuildForm(ManageGuildFormFactory $factory): Form {
    $form = $factory->create($this->model->getUserGuild()->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function handleUpgrade() {
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
  
  /**
   * @return void
   */
  function actionMembers() {
    if(!$this->model->canManage()) {
      $this->flashMessage("Nemůžeš spravovat cech.");
      $this->redirect("Homepage:");
    }
    $guild = $this->model->getUserGuild()->id;
    $this->template->members = $this->model->getMembers($guild);
    $this->template->maxRank = $this->model->maxRank;
  }
  
  /**
   * @param int $user
   * @return void
   */
  function handlePromote(int $user) {
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
  
  /**
   * @param int $user
   * @return void
   */
  function handleDemote(int $user) {
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
  
  /**
   * @param int $user
   * @return void
   */
  function handleKick(int $user) {
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