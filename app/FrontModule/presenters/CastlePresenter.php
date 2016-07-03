<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CastleNotFoundException,
    Nexendrie\Forms\BuildCastleFormFactory,
    Nexendrie\Forms\ManageCastleFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\CannotUpgradeCastleException,
    Nexendrie\Model\CannotRepairCastleException,
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
  function actionDefault() {
    $castle = $this->model->getUserCastle();
    if(!$castle) {
      $this->flashMessage("Nemáš hrad.");
      if($this->profileModel->getPath() === GroupEntity::PATH_TOWER) $this->redirect("build");
      else $this->redirect("Homepage:");
    }
    $this->template->castle = $castle;
    $this->template->canUpgrade = $this->model->canUpgrade();
  }
  
  /**
   * @return void
   */
  function renderList() {
    $this->template->castles = $this->model->listOfCastles();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    try {
      $this->template->castle = $this->model->getCastle($id);
    } catch(CastleNotFoundException $e) {
      throw new \Nette\Application\BadRequestException;
    }
  }
  
  /**
   * @return void
   */
  function actionBuild() {
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
  
  /**
   * @param BuildCastleFormFactory $factory
   * @return Form
   */
  protected function createComponentBuildCastleForm(BuildCastleFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Hrad postaven.");
      $this->redirect("default");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function handleUpgrade() {
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
  
  /**
   * @return void
   */
  function handleRepair() {
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
  
  /**
   * @param ManageCastleFormFactory $factory
   * @return Form
   */
  protected function createComponentManageCastleForm(ManageCastleFormFactory $factory) {
    $form = $factory->create($this->template->castle->id);
    $form->onSuccess[] = function() {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
}
?>