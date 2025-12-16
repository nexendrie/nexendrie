<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;

/**
 * Factory for form AddEditJobMessage
 *
 * @author Jakub Konečný
 */
final class AddEditJobMessageFormFactory
{
    public function create(): Form
    {
        $form = new Form();
        $form->addTextArea("message", "Zpráva:")
            ->setRequired("Zadej zprávu.");
        $form->addCheckbox("success", "Zobrazit při úspěchu");
        $form->addSubmit("submit", "Odeslat");
        return $form;
    }
}
