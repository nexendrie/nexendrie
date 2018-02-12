<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\TakeLoanFormFactory,
    Nexendrie\Forms\OpenDepositAccountFormFactory,
    Nette\Application\UI\Form,
    Nexendrie\Model\NoLoanException,
    Nexendrie\Model\InsufficientFundsException,
    Nexendrie\Model\NoDepositAccountException,
    Nexendrie\Model\DepositAccountNotDueException;
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
    $this->template->loanInterest = $this->sr->settings["fees"]["loanInterest"];
    $this->template->depositInterest = \Nexendrie\Model\Bank::DEPOSIT_INTEREST;
    if(!$this->user->isLoggedIn()) {
      return;
    }
    $this->template->loan = $this->model->getActiveLoan();
    if(!is_null($this->template->loan)) {
      $returnMoney = $this->template->loan->amount + $this->template->loan->interest;
      $this->template->returnMoney = $this->localeModel->money($returnMoney);
    }
    $this->template->deposit = $this->model->getActiveDeposit();
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
  
  protected function createComponentOpenDepositAccountForm(OpenDepositAccountFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function() {
      $this->flashMessage("Termínovaný účet otevřen.");
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
  
  public function actionClose(): void {
    $this->requiresLogin();
    try {
      $this->model->closeDeposit();
      $this->flashMessage("Termínovaný účet uzavřen.");
    } catch(NoDepositAccountException $e) {
      $this->flashMessage("Nemáš otevřený termínovaný účet.");
    } catch(DepositAccountNotDueException $e) {
      $this->flashMessage("Termínovaný účet není splatný.");
    }
    $this->redirect("default");
  }
}
?>