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
  /** @var \Nexendrie\Model\Market @autowire */
  protected $marketModel;
  /** @var \Nexendrie\Model\Job @autowire */
  protected $jobModel;
  /** @var \Nexendrie\Model\Town @autowire */
  protected $townModel;
  /** @var \Nexendrie\Model\Mount @autowire */
  protected $mountModel;
  /** @var \Nexendrie\Model\Skills @autowire */
  protected $skillsModel;
  /** @var \Nexendrie\Model\Tavern @autowire */
  protected $tavernModel;
  /** @var \Nexendrie\Model\Adventure @autowire */
  protected $adventureModel;
  /** @var \Nexendrie\Model\ItemSet @autowire */
  protected $itemSetModel;
  
  /**
   * @return void
   */
  protected function startup() {
    parent::startup();
    $this->requiresPermissions("content", "list");
  }
  
  /**
   * @return void
   */
  function renderShops() {
    $this->template->shops = $this->marketModel->listOfShops();
  }
  
  /**
   * @return void
   */
  function renderItems() {
    $this->template->items = $this->marketModel->listOfItems();
  }
  
  /**
   * @return void
   */
  function renderJobs() {
    $this->template->jobs = $this->jobModel->listOfJobs();
  }
  
  /**
   * @return void
   */
  function renderTowns() {
    $this->template->towns = $this->townModel->listOfTowns();
  }
  
  /**
   * @return void
   */
  function renderMounts() {
    $this->template->mounts = $this->mountModel->listOfMounts();
  }
  
  /**
   * @return void
   */
  function renderSkills() {
    $this->template->skills = $this->skillsModel->listOfSkills();
  }
  
  /**
   * @return void
   */
  function renderMeals() {
    $this->template->meals = $this->tavernModel->listOfMeals();
  }
  
  /**
   * @return void
   */
  function renderAdventures() {
    $this->template->adventures = $this->adventureModel->listOfAdventures();
  }
  
  /**
   * @return void
   */
  function renderItemSets() {
    $this->template->sets = $this->itemSetModel->listOfSets();
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionGift(int $id = 0) {
    $this->requiresPermissions("content", "gift");
  }
  
  /**
   * @param GiftFormFactory $factory
   * @return Form
   */
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