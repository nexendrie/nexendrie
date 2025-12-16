<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Bank;
use Nextras\FormComponents\Controls\DateControl;

/**
 *  Factory for form OpenDepositAccount
 *
 * @author Jakub Konečný
 */
final class OpenDepositAccountFormFactory
{
    public function __construct(private readonly Bank $model)
    {
    }

    public function create(): Form
    {
        $maxDeposit = $this->model->maxDeposit();
        $form = new Form();
        $form->addText("amount", "Částka:")
            ->setRequired("Zadej částku.")
            ->addRule(Form::INTEGER, "Částka musí být celé číslo.")
            ->addRule(Form::RANGE, "Částka musí být v rozmezí 1-$maxDeposit.", [1, $maxDeposit]);
        $term = new DateControl("Termín:");
        $term->setRequired("Zadej datum.");
        $term->addRule(static function (DateControl $datePicker): bool {
            return $datePicker->value->getTimestamp() > time();
        }, "Datum nemůže být v minulosti.");
        $form->addComponent($term, "term");
        $form->addSubmit("submit", "Otevřít účet");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        $this->model->openDeposit($values["amount"], $values["term"]->getTimestamp());
    }
}
