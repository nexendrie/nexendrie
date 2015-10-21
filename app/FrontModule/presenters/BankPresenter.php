<?php
namespace Nexendrie\FrontModule\Presenters;

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
  /** @var \Nexendrie\Model\Bank @autowire */
  protected $model;
  /** @var \Nexendrie\Model\Locale @autowire */
  protected $localeModel;
  /** @var \Nexendrie\Model\SettingsRepository @autowire */
  protected $sr;
  
  /**
   * @return void
   */
  function renderDefault() {
    if($this->user->isLoggedIn()) $this->template->loan = $this->model->getActiveLoan();
    $this->template->maxLoan = $this->localeModel->money($this->model->maxLoan());
    $this->template->interest = $this->sr->settings["fees"]["loanInterest"];
    if($this->user->isLoggedIn() AND $this->template->loan) {
      $returnMoney = $this->template->loan->amount + $this->model->calculateInterest($this->template->loan);
      $this->template->returnMoney = $this->localeModel->money($returnMoney);
    }
  }
  
  /**
   * @param TakeLoanFormFactory $factory
   * @return Form
   */
  protected function createComponentTakeLoanForm(TakeLoanFormFactory $factory) {
    $form = $factory->create();
    $form->onSuccess[] = function(Form $form, array $values) {
      $message = sprintf("Přijal jsi půjčku %s.", $this->localeModel->money($values["amount"]));
      $this->flashMessage($message);
    };
    return $form;
  }
  
  /**
   * @return void
   */
  function actionReturn() {
    $this->requiresLogin();
    try {
      $this->model->returnLoan();
      $this->flashMessage("Vrátil jsi půjčku.");
    } catch(NoLoanException $e) {
      $this->flashMessage("Nemáš žádnou půjčku.");
    } catch(InsufficientFundsException $e) {
      $this->flashMessage("Nemáš dostatek peněz.");
    }
    $this->redirect("default");
  }
}
?>