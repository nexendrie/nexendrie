<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\FoundGuildFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\GuildNotFoundException,
    Nexendrie\Model\CannotLeaveGuildException,
    Nexendrie\Model\CannotJoinGuildException;

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
      $this->flashMessage("Nejsi v cechu");
      $this->redirect("Homepage:");
    }
    $this->template->guild = $guild;
    $this->template->canLeave = $this->model->canLeave();
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
      $this->flashMessage("Vstoupil jsi do cechu.");
      $this->redirect("default");
    } catch(CannotJoinGuildException $e) {
      $this->flashMessage("Nemůžeš vstoupit cech.");
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
      $this->flashMessage("Opustil jsi cech.");
      $this->redirect("Homepage:");
    } catch(CannotLeaveGuildException $e) {
      $this->flashMessage("Nemůžeš opustit cech.");
      $this->redirect("Homepage:");
    }
  }
}
?>