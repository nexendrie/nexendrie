<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Monastery;
use Nexendrie\Model\NotInMonasteryException;
use Nexendrie\Model\InsufficientFundsException;

/**
 * Factory for form MonasteryDonate
 *
 * @author Jakub Konečný
 */
final readonly class MonasteryDonateFormFactory
{
    public function __construct(private Monastery $model)
    {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addInteger("amount", "Množství:")
            ->setRequired("Zadej množství.")
            ->addRule(Form::MIN, "Musíš darovat minimálně 1 groš.", 1);
        $form->addSubmit("submit", "Darovat");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->model->donate($values["amount"]);
        } catch (NotInMonasteryException) {
            $form->addError("Nejsi v klášteře.");
        } catch (InsufficientFundsException) {
            $form->addError("Nemáš dostatek peněz.");
        }
    }
}
