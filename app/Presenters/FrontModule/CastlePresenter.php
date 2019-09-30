<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CastleNotFoundException;
use Nexendrie\Forms\BuildCastleFormFactory;
use Nexendrie\Forms\ManageCastleFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\CannotUpgradeCastleException;
use Nexendrie\Model\CannotRepairCastleException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter Castle
 *
 * @author Jakub Konečný
 */
final class CastlePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Castle */
  protected $model;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Model\UserManager */
  protected $userManager;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  
  public function __construct(\Nexendrie\Model\Castle $model, \Nexendrie\Model\Locale $localeModel, \Nexendrie\Model\UserManager $userManager, \Nexendrie\Model\Profile $profileModel) {
    parent::__construct();
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->userManager = $userManager;
    $this->profileModel = $profileModel;
  }
  
  protected function startup(): void {
    parent::startup();
    if($this->action !== "detail" && $this->action !== "list") {
      $this->requiresLogin();
    }
  }
  
  public function actionDefault(): void {
    $castle = $this->model->getUserCastle();
    if($castle === null) {
      $this->flashMessage("Nemáš hrad.");
      if($this->profileModel->getPath() === GroupEntity::PATH_TOWER) {
        $this->redirect("build");
      }
      $this->redirect("Homepage:");
    }
    $this->template->castle = $castle;
    $this->template->canUpgrade = $this->model->canUpgrade();
  }
  
  public function renderList(): void {
    $this->template->castles = $this->model->listOfCastles();
  }
  
  /**
   * @throws \Nette\Application\BadRequestException
   */
  public function renderDetail(int $id): void {
    try {
      $this->template->castle = $this->model->getCastle($id);
    } catch(CastleNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionBuild(): void {
    $user = $this->userManager->get($this->user->id);
    if($user->group->path !== GroupEntity::PATH_TOWER) {
      $this->flashMessage("Nejsi šlechtic.");
      $this->redirect("Homepage:");
    } elseif($this->model->getUserCastle() !== null) {
      $this->flashMessage("Můžeš postavit jen 1 hrad.");
      $this->redirect("default");
    }
    $this->template->buildingPrice = $this->localeModel->money($this->sr->settings["fees"]["buildCastle"]);
  }
  
  protected function createComponentBuildCastleForm(BuildCastleFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Hrad postaven.");
      $this->redirect("default");
    };
    return $form;
  }
  
  public function handleUpgrade(): void {
    try {
      $this->model->upgrade();
      $this->flashMessage("Hrad vylepšen.");
      $this->redirect("default");
    } catch(CannotUpgradeCastleException $e) {
      $this->flashMessage("Nemůžeš vylepšit hrad.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
  
  public function handleRepair(): void {
    try {
      $this->model->repair();
      $this->flashMessage("Hrad opraven.");
      $this->redirect("default");
    } catch(CannotRepairCastleException $e) {
      $this->flashMessage("Nemůžeš opravit hrad.");
      $this->redirect("Homepage:");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nedostatek peněz.");
      $this->redirect("manage");
    }
  }
  
  protected function createComponentManageCastleForm(ManageCastleFormFactory $factory): Form {
    $form = $factory->create($this->template->castle->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>