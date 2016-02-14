<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\FoundGuildFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\GuildNotFoundException,
    Nexendrie\Model\CannotLeaveGuildException,
    Nexendrie\Model\CannotJoinGuildException,
    Nexendrie\Forms\ManageGuildFormFactory,
    Nexendrie\Model\CannotUpgradeGuildException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Orm\User as UserEntity;

/**
 * Presenter Guild
 *
 * @author Jakub Konečný
 */
class GuildPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Guild @autowire */
  protected $model;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") $this->requiresLogin();
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
   */
  function renderDetail($id) {
    try {
      $this->template->guild = $this->model->getGuild($id);
    } catch(GuildNotFoundException $e) {
      $this->forward("notfound");
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
  }
  
  /**
   * @param FoundGuildFormFactory $factory
   * @return Form
   */
  protected function createComponentFoundGuildForm(FoundGuildFormFactory $factory) {
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
   */
  function actionJoin($id) {
    try {
      $this->model->join($id);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Vstoupila jsi do cechu.";
      else $message = "Vstoupil jsi do cechu.";
      $this->flashMessage($message);
      $this->redirect("default");
    } catch(CannotJoinGuildException $e) {
      $this->flashMessage("Nemůžeš vstoupit do cechu.");
      $this->redirect("Homepage:");
    } catch(GuildNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @return void
   */
  function actionLeave() {
    try {
      $this->model->leave();
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) $message = "Opustila jsi cech.";
      else $message = "Opustil jsi cech.";
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
      $this->template->guild = $monastery = $this->model->getUserGuild();
      $this->template->canUpgrade = $this->model->canUpgrade();
    }
  }
  
  /**
   * @param ManageGuildFormFactory $factory
   * @return Form
   */
  protected function createComponentManageGuildForm(ManageGuildFormFactory $factory) {
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
}
?>