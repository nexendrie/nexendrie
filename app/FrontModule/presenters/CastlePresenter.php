<?php
namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\CastleNotFoundException,
    Nexendrie\Forms\BuildCastleFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\CannotUpgradeCastleException,
    Nexendrie\Model\CannotRepairCastleException;

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
    $castle = $this->model->getUserCastle();
    if(!$castle) {
      $this->flashMessage("Nemáš hrad.");
      $this->redirect("Homepage");
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
      $this->forward("notfound");
    }
  }
  
  /**
   * @return void
   */
  function actionBuild() {
    $user = $this->userManager->get($this->user->id);
    if($user->group->path != "tower") {
      $this->flashMessage("Nejsi šlechtic.");
      $this->redirect("Homepage:");
    } elseif($this->model->getUserCastle()) {
      $this->flashMessage("Můžeš postavit jen 1 hrad.");
      $this->redirect("default");
    }
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
}
?>