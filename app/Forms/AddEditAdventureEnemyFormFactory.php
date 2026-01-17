<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditAdventureEnemy
 *
 * @author Jakub Konečný
 */
final class AddEditAdventureEnemyFormFactory
{
    public function create(): Form
    {
        $form = new Form();
        $form->addText("name", "Jméno:")
            ->setRequired("Zadej jméno.")
            ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 20 znaků.", 20);
        $form->addInteger("order", "Pořadí:")
            ->setRequired("Zadej pořadí.")
            ->addRule(Form::RANGE, "Pořadí musí být v rozmezí 1-9.", [1, 9]);
        $form->addInteger("hitpoints", "Životy:")
            ->setRequired("Zadej počet životů.")
            ->addRule(Form::RANGE, "Počet životů musí být v rozmezí 1-999.", [1, 999]);
        $form->addInteger("strength", "Síla:")
            ->setRequired("Zadej sílu.")
            ->addRule(Form::RANGE, "Síla musí být v rozmezí 1-99.", [1, 99]);
        $form->addInteger("armor", "Brnění:")
            ->setRequired("Zadej brnění.")
            ->addRule(Form::RANGE, "Brnění musí být v rozmezí 0-99.", [0, 99]);
        $form->addInteger("initiative", "Iniciativa:")
            ->setRequired("Zadej iniciativu.")
            ->addRule(Form::RANGE, "Iniciativa musí být v rozmezí 0-99.", [0, 99]);
        $form->addInteger("reward", "Odměna:")
            ->setRequired("Zadej odměnu.")
            ->addRule(Form::RANGE, "Odměna musí být v rozmezí 1-999.", [1, 999]);
        $form->addTextArea("encounterText", "Text při střetnutí:")
            ->setRequired("Zadej text při střetnutí.");
        $form->addTextArea("victoryText", "Text při vítězství:")
            ->setRequired("Zadej text při vítězství.");
        $form->addSubmit("submit", "Odeslat");
        return $form;
    }
}
