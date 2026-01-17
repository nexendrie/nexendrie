<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\CannotFoundGuildException;
use Nexendrie\Model\Guild;
use Nexendrie\Model\GuildNameInUseException;
use Nexendrie\Model\InsufficientFundsException;
use Nexendrie\Model\Skills;

/**
 * Factory for form FoundGuild
 *
 * @author Jakub Konečný
 */
final class FoundGuildFormFactory
{
    public function __construct(private readonly Guild $model, private readonly Skills $skillsModel)
    {
    }

    private function getListOfSkills(): array
    {
        return $this->skillsModel->listOfSkills("work")->fetchPairs("id", "name");
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addText("name", "Jméno:")
            ->setRequired("Zadej jméno.")
            ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25);
        $form->addTextArea("description", "Popis:")
            ->setRequired("Zadej popis.");
        $form->addSelect("skill", "Dovednost:", $this->getListOfSkills())
            ->setRequired("Vyber dovednost.");
        $form->addSubmit("submit", "Založit");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->model->found($values);
        } catch (CannotFoundGuildException) {
            $form->addError("Nemůžeš založit cech.");
        } catch (GuildNameInUseException) {
            $form->addError("Zadané jméno je již zabráno.");
        } catch (InsufficientFundsException) {
            $form->addError("Nemáš dostatek peněz.");
        }
    }
}
