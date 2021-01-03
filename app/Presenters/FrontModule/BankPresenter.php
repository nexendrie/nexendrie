<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\TakeLoanFormFactory;
use Nexendrie\Forms\OpenDepositAccountFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\NoLoanException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\NoDepositAccountException;
use Nexendrie\Model\DepositAccountNotDueException;

/**
 * Presenter Bank
 *
 * @author Jakub Konečný
 */
final class BankPresenter extends BasePresenter {
  protected \Nexendrie\Model\Bank $model;
  protected \Nexendrie\Model\Locale $localeModel;
  protected bool $cachingEnabled = false;
  
  public function __construct(\Nexendrie\Model\Bank $model, \Nexendrie\Model\Locale $localeModel) {
    parent::__construct();
    $this->model = $model;
    $this->localeModel = $localeModel;
  }
  
  public function renderDefault(): void {
    $this->template->maxLoan = $this->model->maxLoan();
    $this->template->loanInterest = $this->sr->settings["fees"]["loanInterest"];
    $this->template->depositInterest = $this->sr->settings["fees"]["depositInterest"];
    if(!$this->user->isLoggedIn()) {
      return;
    }
    $this->template->loan = $this->model->getActiveLoan();
    if($this->template->loan !== null) {
      $this->template->returnMoney = $this->template->loan->amount + $this->template->loan->interest;
    }
    $this->template->deposit = $this->model->getActiveDeposit();
  }
  
  protected function createComponentTakeLoanForm(TakeLoanFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values): void {
      $text = $this->localeModel->genderMessage("Přijal(a) jsi půjčku %s.");
      $message = sprintf($text, $this->localeModel->money($values["amount"]));
      $this->flashMessage($message);
    };
    return $form;
  }
  
  protected function createComponentOpenDepositAccountForm(OpenDepositAccountFormFactory $factory): Form {
    $form = $factory->create();
    $form->onSuccess[] = function(): void {
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