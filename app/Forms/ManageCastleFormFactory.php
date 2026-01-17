<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Castle;
use Nexendrie\Model\CastleNameInUseException;

/**
 * Factory for form ManageCastle
 *
 * @author Jakub Konečný
 */
final class ManageCastleFormFactory
{
    private int $id;

    public function __construct(private readonly Castle $model)
    {
    }

    public function create(int $castleId): Form
    {
        $form = new Form();
        $this->id = $castleId;
        $castle = $this->model->getCastle($this->id);
        $form->addText("name", "Jméno:")
            ->setRequired("Zadej jméno.")
            ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
        $form->addTextArea("description", "Popis:")
            ->setRequired("Zadej popis.");
        $form->addSubmit("submit", "Odeslat");
        $form->setDefaults($castle->toArray());
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->model->editCastle($this->id, $values);
        } catch (CastleNameInUseException) {
            $form->addError("Zadané jméno je již zabráno.");
        }
    }
}
