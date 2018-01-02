<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\TakeLoanFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\NoLoanException,
    Nexendrie\Model\InsufficientFundsException;
/**
 * Presenter Bank
 *
 * @author Jakub Konečný
 */
class BankPresenter extends BasePresenter {
  /** @var \Nexendrie\Model\Bank */
  protected $model;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Model\SettingsRepository */
  protected $sr;
  
  public function __construct(\Nexendrie\Model\Bank $model, \Nexendrie\Model\Locale $localeModel, \Nexendrie\Model\SettingsRepository $sr) {
    parent::__construct();
    $this->model = $model;
    $this->localeModel = $localeModel;
    $this->sr = $sr;
  }
  
  public function renderDefault(): void {
    $this->template->maxLoan = $this->localeModel->money($this->model->maxLoan());
    $this->template->interest = $this->sr->settings["fees"]["loanInterest"];
    if(!$this->user->isLoggedIn()) {
      return;
    }
    $this->template->loan = $this->model->getActiveLoan();
    if(!is_null($this->template->loan)) {
      $returnMoney = $this->template->loan->amount + $this->model->calculateInterest($this->template->loan);
      $this->template->returnMoney = $this->localeModel->money($returnMoney);
    }
  }
  
  protected function createComponentTakeLoanForm(TakeLoanFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $text = $this->localeModel->genderMessage("Přijal(a) jsi půjčku %s.");
      $message = sprintf($text, $this->localeModel->money($values["amount"]));
      $this->flashMessage($message);
    };
    return $form;
  }
  
  public function actionReturn(): void {
    $this->requiresLogin();
    try {
      $this->model->returnLoan();
      $message = $this->localeModel->genderMessage("Vrátil(a) jsi půjčku.");
      $this->flashMessage($message);
    } catch(NoLoanException $e) {
      $this->flashMessage("Nemáš žádnou půjčku.");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nemáš dostatek peněz.");
    }
    $this->redirect("default");
  }
}
?>