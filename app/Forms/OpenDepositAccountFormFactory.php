<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\DateTimeControl;
use Nexendrie\Model\Bank;

/**
 *  Factory for form OpenDepositAccount
 *
 * @author Jakub Konečný
 */
final readonly class OpenDepositAccountFormFactory
{
    public function __construct(private Bank $model)
    {
    }

    public function create(): Form
    {
        $maxDeposit = $this->model->maxDeposit();
        $form = new Form();
        $form->addInteger("amount", "Částka:")
            ->setRequired("Zadej částku.")
            ->addRule(Form::Range, "Částka musí být v rozmezí 1-$maxDeposit.", [1, $maxDeposit]);
        $form->addDate("term", "Termín:")
            ->setRequired("Zadej datum.")
            ->addRule(static function (DateTimeControl $datePicker): bool {
                return $datePicker->value->getTimestamp() > time();
            }, "Datum nemůže být v minulosti.");
        $form->addSubmit("submit", "Otevřít účet");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function process(Form $form, array $values): void
    {
        $this->model->openDeposit($values["amount"], $values["term"]->getTimestamp());
    }
}
