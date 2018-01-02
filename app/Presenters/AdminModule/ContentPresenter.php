<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\GiftFormFactory,
    Nette\Application\UI\Form;

/**
 * Presenter Content
 *
 * @author Jakub Konečný
 */
class ContentPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Market */
  protected $marketModel;
  /** @var \Nexendrie\Model\Job */
  protected $jobModel;
  /** @var \Nexendrie\Model\Town */
  protected $townModel;
  /** @var \Nexendrie\Model\Mount */
  protected $mountModel;
  /** @var \Nexendrie\Model\Skills */
  protected $skillsModel;
  /** @var \Nexendrie\Model\Tavern */
  protected $tavernModel;
  /** @var \Nexendrie\Model\Adventure */
  protected $adventureModel;
  /** @var \Nexendrie\Model\ItemSet */
  protected $itemSetModel;
  
  public function __construct(\Nexendrie\Model\Market $marketModel, \Nexendrie\Model\Job $jobModel, \Nexendrie\Model\Town $townModel, \Nexendrie\Model\Mount $mountModel, \Nexendrie\Model\Skills $skillsModel, \Nexendrie\Model\Tavern $tavernModel, \Nexendrie\Model\Adventure $adventureModel, \Nexendrie\Model\ItemSet $itemSetModel) {
    parent::__construct();
    $this->marketModel = $marketModel;
    $this->jobModel = $jobModel;
    $this->townModel = $townModel;
    $this->mountModel = $mountModel;
    $this->skillsModel = $skillsModel;
    $this->tavernModel = $tavernModel;
    $this->adventureModel = $adventureModel;
    $this->itemSetModel = $itemSetModel;
  }
  
  protected function startup(): void {
    parent::startup();
    $this->requiresPermissions("content", "list");
  }
  
  public function renderShops(): void {
    $this->template->shops = $this->marketModel->listOfShops();
  }
  
  public function renderItems(): void {
    $this->template->items = $this->marketModel->listOfItems();
  }
  
  public function renderJobs(): void {
    $this->template->jobs = $this->jobModel->listOfJobs();
  }
  
  public function renderTowns(): void {
    $this->template->towns = $this->townModel->listOfTowns();
  }
  
  public function renderMounts(): void {
    $this->template->mounts = $this->mountModel->listOfMounts();
  }
  
  public function renderSkills(): void {
    $this->template->skills = $this->skillsModel->listOfSkills();
  }
  
  public function renderMeals(): void {
    $this->template->meals = $this->tavernModel->listOfMeals();
  }
  
  public function renderAdventures(): void {
    $this->template->adventures = $this->adventureModel->listOfAdventures();
  }
  
  public function renderItemSets(): void {
    $this->template->sets = $this->itemSetModel->listOfSets();
  }
  
  public function actionGift(int $id = 0): void {
    $this->requiresPermissions("content", "gift");
  }
  
  protected function createComponentGiftForm(GiftFormFactory $factory): Form {
    $form = $factory->create();
    $user = $this->getParameter("id");
    if($user > 0) {
      $form["user"]->setDefaultValue($user);
    }
    $form->onSuccess[] = function() {
      $this->flashMessage("Odesláno.");
    };
    return $form;
  }
}
?>