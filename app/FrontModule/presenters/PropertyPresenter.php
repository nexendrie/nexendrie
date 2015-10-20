<?php
namespace Nexendrie\FrontModule\Presenters;

use Nexendrie\Orm\Town as TownEntity,
    Nexendrie\Model\TownNotFoundException,
    Nexendrie\Forms\ManageTownFormFactory,
    Nette\Application\UI\Form;

/**
 * Presenter Assets
 *
 * @author Jakub Konečný
 */
class PropertyPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Property @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Town @autowire */
  protected $townModel;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  /** @var TownEntity */
  private $town;
  
  function startup() {
    parent::startup();
    $this->requiresLogin();
    $this->mustNotBeBanned();
  }
  
  /**
   * @return void
   */
  function renderDefault() {
    $data = $this->model->show();
    $this->template->money = $data["money"];
    $this->template->items = $data["items"];
    $this->template->isLord = $data["isLord"];
    $this->template->towns = $data["towns"];
  }
  
  /**
   * @param int $id
   * @return void
   */
  function actionTown($id) {
    try {
      $this->town = $this->townModel->get($id);
    } catch(TownNotFoundException $e) {
      $this->flashMessage("Město nenalezeno.");
      $this->redirect("Homepage:");
    }
    if($this->town->owner->id != $this->user->id) {
      $this->flashMessage("Zadané město ti nepatří.");
      $this->redirect("Homepage:");
    }
  }
  
  /**
   * @param int $id
   * @return void
   */
  function renderTown($id) {
    $this->template->town = $this->town;
  }
  
  /**
   * @param ManageTownFormFactory $factory
   * @return Form
   */
  protected function createComponentManageTownForm(ManageTownFormFactory $factory) {
    $form = $factory->create($this->town->id);
    $form->onSuccess[] = function(Form $form) {
      $this->flashMessage("Změny uloženy.");
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function renderBudget() {
    $budget = $this->model->budget();
    $this->template->incomes = $this->localeModel->money(array_sum($budget["incomes"]));
    $this->template->expenses = $this->localeModel->money(array_sum($budget["expenses"]));
    $this->template->budget = $this->localeModel->money(array_sum($budget["incomes"]) - array_sum($budget["expenses"]));
    $this->template->work = $this->localeModel->money($budget["incomes"]["work"]);
    $this->template->adventures = $this->localeModel->money($budget["incomes"]["adventures"]);
    $this->template->taxes = $this->localeModel->money($budget["incomes"]["taxes"]);
    $this->template->incomeTax = $this->localeModel->money($budget["expenses"]["incomeTax"]);
  }
}
?>