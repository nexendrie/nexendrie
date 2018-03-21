<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\TownNotFoundException,
    Nexendrie\Model\CannotMoveToSameTownException,
    Nexendrie\Model\CannotMoveToTownException,
    Nexendrie\Forms\FoundTownFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Components\IElectionsControlFactory,
    Nexendrie\Components\ElectionsControl,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
class TownPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Town */
  protected $model;
  /** @var \Nexendrie\Model\UserManager */
  protected $userManager;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  /** @var \Nexendrie\Model\House */
  protected $houseModel;
  /** @var \Nexendrie\Model\Guild */
  protected $guildModel;
  /** @var \Nexendrie\Model\Order */
  protected $orderModel;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Orm\Town */
  private $town;
  
  /**
   */
  public function __construct(\Nexendrie\Model\Town $model, \Nexendrie\Model\UserManager $userManager, \Nexendrie\Model\Profile $profileModel, \Nexendrie\Model\House $houseModel, \Nexendrie\Model\Guild $guildModel, \Nexendrie\Model\Order $orderModel, \Nexendrie\Model\Locale $localeModel) {
    parent::__construct();
    $this->model = $model;
    $this->userManager = $userManager;
    $this->profileModel = $profileModel;
    $this->houseModel = $houseModel;
    $this->guildModel = $guildModel;
    $this->orderModel = $orderModel;
    $this->localeModel = $localeModel;
  }
  
  protected function startup(): void {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") {
      $this->requiresLogin();
    }
  }
  
  public function renderDefault(): void {
    $this->template->town = $this->model->get($this->user->identity->town);
    $user = $this->userManager->get($this->user->id);
    $this->template->path = $user->group->path;
    $this->template->house = $this->houseModel->getUserHouse();
    $this->template->guild = $this->guildModel->getUserGuild();
    $this->template->order = $this->orderModel->getUserOrder();
  }
  
  public function renderList(): void {
    $this->template->towns = $this->model->listOfTowns();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderDetail(int $id): void {
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
  
  public function actionMove(int $id): void {
    try {
      $this->model->moveToTown($id);
      $message = $this->localeModel->genderMessage("Přestěhoval(a) jsi se do vybraného města.");
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
  
  public function actionFound(): void {
    $path = $this->profileModel->getPath();
    if($path != GroupEntity::PATH_TOWER) {
      $this->flashMessage("Jen šlechtici mohou zakládat města.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentFoundTownForm(FoundTownFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Město založeno.");
      $this->redirect("Homepage:");
    };
    return $form;
  }
  
  public function actionElections(): void {
    $this->requiresPermissions("town", "elect");
    $this->town = $this->model->get($this->user->identity->town);
  }
  
  protected function createComponentElections(IElectionsControlFactory $factory): ElectionsControl {
    $elections = $factory->create();
    $elections->town = $this->town;
    return $elections;
  }
}
?>