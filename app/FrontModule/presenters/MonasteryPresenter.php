<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Model\MonasteryNotFoundException,
    Nexendrie\Model\NotInMonasteryException,
    Nexendrie\Model\CannotJoinMonasteryException,
    Nexendrie\Model\CannotPrayException,
    Nexendrie\Model\CannotLeaveMonasteryException,
    Nexendrie\Forms\BuildMonasteryFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Forms\MonasteryDonateFormFactory;

/**
 * Presenter Monastery
 *
 * @author Jakub Konečný
 */
class MonasteryPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Monastery @autowire */
  protected $model;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->requiresLogin();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    try {
      $this->template->monastery = $this->model->getByUser();
      $this->template->canPray = $this->model->canPray();
      $this->template->canLeave = $this->model->canLeave();
      $this->template->canBuild = $this->model->canBuild();
    } catch(NotInMonasteryException $e) {
      $this->flashMessage("Nejsi v klášteře.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->monasteries = $this->model->listOfMonasteries();
    $this->template->canJoin = $this->model->canJoin();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->monastery = $this->model->get($id);
    } catch(MonasteryNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @return void
   */
  function actionBuild() {
    if(!$this->model->canBuild()) {
      $this->flashMessage("Nemůžeš postavit klášter.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param BuildMonasteryFormFactory $factory
   * @return Form
   */
  protected function createComponentBuildMonasteryForm(BuildMonasteryFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[]= function(Form $form) {
      $this->flashMessage("Klášter založen.");
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
      $this->redirect("default");
    } catch(CannotJoinMonasteryException $e) {
      $this->flashMessage("Nemůžeš vstoupit do kláštera.");
      $this->redirect("Homepage:");
    } catch(MonasteryNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @return void
   */
  function actionLeave() {
    try {
      $this->model->leave();
      $this->flashMessage("Vystoupil jsi z kláštera.");
      $this->redirect("Homepage:");
    } catch(CannotLeaveMonasteryException $e) {
      $this->flashMessage("Nemůžeš vystoupit z kláštera.");
      $this->redirect("default");
    }
  }
  
  /**
   * @return void
   */
  function actionPray() {
    try {
      $this->model->pray();
      $this->flashMessage("Modlidba ti přidala 5 životů.");
      $this->redirect("default");
    } catch(CannotPrayException $e) {
      $this->flashMessage("Nemůžeš se modlit (právě teď).");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param MonasteryDonateFormFactory $factory
   * @return Form
   */
  protected function createComponentMonasteryDonateForm(MonasteryDonateFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Příspěvek proveden.");
    };
    return $form;
  }
}
?>