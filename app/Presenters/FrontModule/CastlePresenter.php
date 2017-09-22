<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CastleNotFoundException,
    Nexendrie\Forms\BuildCastleFormFactory,
    Nexendrie\Forms\ManageCastleFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\CannotUpgradeCastleException,
    Nexendrie\Model\CannotRepairCastleException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter Castle
 *
 * @author Jakub Konečný
 */
class CastlePresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Castle @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  /** @var \Nexendrie\Model\UserManager @autowire */
  protected $userManager;
  /** @var \Nexendrie\Model\Profile @autowire */
  protected $profileModel;
  
  protected function startup(): void {
    parent::startup();
    if($this->action != "detail" AND $this->action != "list") {
      $this->requiresLogin();
    }
  }
  
  public function actionDefault(): void {
    $castle = $this->model->getUserCastle();
    if(!$castle) {
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
    if($user->group->path != GroupEntity::PATH_TOWER) {
      $this->flashMessage("Nejsi šlechtic.");
      $this->redirect("Homepage:");
    } elseif($this->model->getUserCastle()) {
      $this->flashMessage("Můžeš postavit jen 1 hrad.");
      $this->redirect("default");
    }
    $this->template->buildingPrice = $this->localeModel->money($this->model->buildingPrice);
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