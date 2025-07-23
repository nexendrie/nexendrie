<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\GiftFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\Adventure;
use Nexendrie\Model\ContentReportNotFoundException;
use Nexendrie\Model\ItemSet;
use Nexendrie\Model\Job;
use Nexendrie\Model\Market;
use Nexendrie\Model\MissingPermissionsException;
use Nexendrie\Model\Moderation;
use Nexendrie\Model\Mount;
use Nexendrie\Model\Skills;
use Nexendrie\Model\Tavern;
use Nexendrie\Model\Town;

/**
 * Presenter Content
 *
 * @author Jakub Konečný
 */
final class ContentPresenter extends BasePresenter {
  public function __construct(private readonly Market $marketModel, private readonly Job $jobModel, private readonly Town $townModel, private readonly Mount $mountModel, private readonly Skills $skillsModel, private readonly Tavern $tavernModel, private readonly Adventure $adventureModel, private readonly ItemSet $itemSetModel, private readonly Moderation $moderationModel) {
    parent::__construct();
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
    $user = (int) $this->getParameter("id");
    if($user > 0) {
      /** @var \Nette\Forms\Controls\SelectBox $userField */
      $userField = $form["user"];
      $userField->setDefaultValue($user);
    }
    $form->onSuccess[] = function(): void {
      $this->flashMessage("Odesláno.");
    };
    return $form;
  }

  public function renderReported(): void {
    $this->requiresPermissions("content", "delete");
    $this->template->reports = $this->moderationModel->getReportedContent();
  }

  public function handleDelete(int $report): void {
    try {
      $this->moderationModel->deleteContent($report);
      $this->flashMessage("Obsah smazán.");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K tomuto nemáš práva.");
      $this->redirect(":Front:Homepage:");
    } catch(ContentReportNotFoundException) {
      $this->flashMessage("Tento obsah (už) není nahlášený.");
      $this->redirect("Homepage:");
    }
    $this->redirect("this");
  }

  public function handleIgnore(int $report): void {
    try {
      $this->moderationModel->ignoreReport($report);
      $this->flashMessage("Nahlášení ignorováno.");
    } catch(MissingPermissionsException) {
      $this->flashMessage("K tomuto nemáš práva.");
      $this->redirect(":Front:Homepage:");
    } catch(ContentReportNotFoundException) {
      $this->flashMessage("Tento obsah (už) není nahlášený.");
      $this->redirect("Homepage:");
    }
    $this->redirect("this");
  }
}
?>