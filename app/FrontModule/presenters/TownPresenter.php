<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\TownNotFoundException,
    Nexendrie\Model\CannotMoveToSameTownException,
    Nexendrie\Model\CannotMoveToTownException,
    Nexendrie\Forms\FoundTownFormFactory,
    Nette\Application\UI\Form;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
class TownPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Town @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Castle @autowire */
  protected $castleModel;
  /** @var \Nexendrie\Model\UserManager @autowire */
  protected $userManager;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  /** @var \Nexendrie\Model\House @autowire */
  protected $houseModel;
  
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
    $this->template->town = $this->model->get($this->user->identity->town);
    $user = $this->userManager->get($this->user->id);
    $this->template->path = $user->group->path;
    $this->template->monastery = $user->monastery;
    $this->template->castle = $this->castleModel->getUserCastle();
    $this->template->house = $this->houseModel->getUserHouse();
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->towns = $this->model->listOfTowns();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->town = $this->model->get($id);
      if($id == $this->user->identity->town) $this->template->canMove = false;
      else $this->template->canMove = $this->model->canMove();
    } catch(TownNotFoundException $e) {
      $this->forward("notfound");
    }
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionMove($id) {
    try {
      $this->model->moveToTown((int) $id);
      $this->flashMessage("Přestěhoval jsi se do vybraného města.");
      $this->redirect("Town:");
    } catch(TownNotFoundException $e) {
      $this->flashMessage("Město nebylo nalezeno.");
      $this->redirect("Homepage:");
    } catch(CannotMoveToSameTownException $e) {
      $this->flashMessage("V tomto městě již žiješ.");
      $this->redirect("Homepage:");
    } catch(CannotMoveToTownException $e) {
      $this->flashMessage("Nemůžeš se přesunout do jiného města.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @return void
   */
  function actionFound() {
    $path = $this->profileModel->getPath();
    if($path != "tower") {
      $this->flashMessage("Jen šlechtici mohou zakládat města.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param FoundTownFormFactory $factory
   * @return Form
   */
  protected function createComponentFoundTownForm(FoundTownFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Město založeno.");
      $this->redirect("Homepage:");
    };
    return $form;
  }
}
?>