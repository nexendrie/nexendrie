<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Forms\TakeLoanFormFactory;
use Nexendrie\Forms\OpenDepositAccountFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Model\Bank;
use Nexendrie\Model\Locale;
use Nexendrie\Model\NoLoanException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\NoDepositAccountException;
use Nexendrie\Model\DepositAccountNotDueException;

/**
 * Presenter Bank
 *
 * @author Jakub Konečný
 */
final class BankPresenter extends BasePresenter
{
    public function __construct(private readonly Bank $model, private readonly Locale $localeModel)
    {
        parent::__construct();
        $this->cachingEnabled = false;
    }

    public function renderDefault(): void
    {
        $this->template->maxLoan = $this->model->maxLoan();
        $this->template->loanInterest = $this->sr->settings["fees"]["loanInterest"];
        $this->template->depositInterest = $this->sr->settings["fees"]["depositInterest"];
        if (!$this->user->isLoggedIn()) {
            return;
        }
        $this->template->loan = $this->model->getActiveLoan();
        if ($this->template->loan !== null) {
            $this->template->returnMoney = $this->template->loan->amount + $this->template->loan->interest;
        }
        $this->template->deposit = $this->model->getActiveDeposit();
    }

    protected function createComponentTakeLoanForm(TakeLoanFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (Form $form, array $values): void {
            $text = $this->localeModel->genderMessage("Přijal(a) jsi půjčku %s.");
            $message = sprintf($text, $this->localeModel->money($values["amount"]));
            $this->flashMessage($message);
        };
        return $form;
    }

    protected function createComponentOpenDepositAccountForm(OpenDepositAccountFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Termínovaný účet otevřen.");
        };
        return $form;
    }

    public function actionReturn(): never
    {
        $this->requiresLogin();
        try {
            $this->model->returnLoan();
            $message = $this->localeModel->genderMessage("Vrátil(a) jsi půjčku.");
            $this->flashMessage($message);
        } catch (NoLoanException) {
            $this->flashMessage("Nemáš žádnou půjčku.");
        } catch (InsufficientFundsException) {
            $this->flashMessage("Nemáš dostatek peněz.");
        }
        $this->redirect("default");
    }

    public function actionClose(): never
    {
        $this->requiresLogin();
        try {
            $this->model->closeDeposit();
            $this->flashMessage("Termínovaný účet uzavřen.");
        } catch (NoDepositAccountException) {
            $this->flashMessage("Nemáš otevřený termínovaný účet.");
        } catch (DepositAccountNotDueException) {
            $this->flashMessage("Termínovaný účet není splatný.");
        }
        $this->redirect("default");
    }
}
