<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\TownNotFoundException,
    Nexendrie\Model\CannotMoveToSameTownException,
    Nexendrie\Model\CannotMoveToTownException,
    Nexendrie\Forms\FoundTownFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Components\ElectionsControlFactory,
    Nexendrie\Components\ElectionsControl,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
class TownPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Town @autowire */
  protected $model;
  /** @var \Nexendrie\Model\UserManager @autowire */
  protected $userManager;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  /** @var \Nexendrie\Model\House @autowire */
  protected $houseModel;
  /** @var \Nexendrie\Model\Guild @autowire */
  protected $guildModel;
  /** @var \Nexendrie\Model\Order @autowire */
  protected $orderModel;
  /** @var \Nexendrie\Orm\Town */
  private $town;
  
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
    $this->template->town = $this->model->get($this->user->identity->town);
    $user = $this->userManager->get($this->user->id);
    $this->template->path = $user->group->path;
    $this->template->house = $this->houseModel->getUserHouse();
    $this->template->guild = $this->guildModel->getUserGuild();
    $this->template->order = $this->orderModel->getUserOrder();
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
   * @throws \Nette\Application\BadRequestException
   */
  function renderDetail(int $id) {
    try {
      $this->template->town = $this->model->get($id);
      if(!$this->user->isLoggedIn()) {
        $this->template->canMove = false;
      } elseif($id == $this->user->identity->town) {
        $this->template->canMove = false;
      } else {
        $this->template->canMove = $this->model->canMove();
      }
    } catch(TownNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionMove(int $id) {
    try {
      $this->model->moveToTown((int) $id);
      if($this->user->identity->gender === UserEntity::GENDER_FEMALE) {
        $message = "Přestěhovala jsi se do vybraného města.";
      } else {
        $message = "Přestěhoval jsi se do vybraného města.";
      }
      $this->flashMessage($message);
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
    if($path != GroupEntity::PATH_TOWER) {
      $this->flashMessage("Jen šlechtici mohou zakládat města.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param FoundTownFormFactory $factory
   * @return Form
   */
  protected function createComponentFoundTownForm(FoundTownFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Město založeno.");
      $this->redirect("Homepage:");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function actionElections() {
    $this->requiresPermissions("town", "elect");
    $this->town = $this->model->get($this->user->identity->town);
  }
  
  /**
   * @param ElectionsControlFactory $factory
   * @return ElectionsControl
   */
  protected function createComponentElections(ElectionsControlFactory $factory): ElectionsControl {
    $elections = $factory->create();
    $elections->town = $this->town;
    return $elections;
  }
}
?>