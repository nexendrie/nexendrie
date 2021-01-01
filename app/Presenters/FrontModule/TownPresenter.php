<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Chat\ITownChatControlFactory;
use Nexendrie\Chat\TownChatControl;
use Nexendrie\Model\TownNotFoundException;
use Nexendrie\Model\CannotMoveToSameTownException;
use Nexendrie\Model\CannotMoveToTownException;
use Nexendrie\Forms\FoundTownFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Components\IElectionsControlFactory;
use Nexendrie\Components\ElectionsControl;
use Nexendrie\Orm\Group as GroupEntity;

/**
 * Presenter Town
 *
 * @author Jakub Konečný
 */
final class TownPresenter extends BasePresenter {
  protected \Nexendrie\Model\Town $model;
  protected \Nexendrie\Model\UserManager $userManager;
  protected \Nexendrie\Model\Profile $profileModel;
  protected \Nexendrie\Model\Locale $localeModel;
  private \Nexendrie\Orm\Town $town;
  private ITownChatControlFactory $chatFactory;
  protected bool $cachingEnabled = false;

  public function __construct(\Nexendrie\Model\Town $model, \Nexendrie\Model\UserManager $userManager, \Nexendrie\Model\Profile $profileModel, \Nexendrie\Model\Locale $localeModel, ITownChatControlFactory $chatFactory) {
    parent::__construct();
    $this->model = $model;
    $this->userManager = $userManager;
    $this->profileModel = $profileModel;
    $this->localeModel = $localeModel;
    $this->chatFactory = $chatFactory;
  }
  
  protected function startup(): void {
    parent::startup();
    if($this->action !== "detail" && $this->action !== "list") {
      $this->requiresLogin();
    }
  }

  protected function getChat(): ?TownChatControl {
    return $this->chatFactory->create();
  }

  public function renderDefault(): void {
    $user = $this->userManager->get($this->user->id);
    $this->template->town = $user->town;
    $this->template->path = $user->group->path;
    $this->template->house = $user->house;
    $this->template->guild = $user->guild;
    $this->template->order = $user->order;
    $this->publicCache = false;
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
      } elseif($id === $this->user->identity->town) {
        $this->template->canMove = false;
      } else {
        $this->template->canMove = $this->model->canMove();
      }
      $this->template->canManage = $this->model->canManage($this->template->town);
    } catch(TownNotFoundException $e) {
      throw new \Nette\Application\BadRequestException();
    }
  }
  
  public function actionMove(int $id): void {
    try {
      $this->model->moveToTown($id);
      /** @var \Nexendrie\Model\Authenticator $authenticator */
      $authenticator = $this->user->authenticator;
      $authenticator->user = $this->user;
      $authenticator->refreshIdentity();
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
    if($path !== GroupEntity::PATH_TOWER) {
      $this->flashMessage("Jen šlechtici mohou zakládat města.");
      $this->redirect("Homepage:");
    }
  }
  
  protected function createComponentFoundTownForm(FoundTownFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
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