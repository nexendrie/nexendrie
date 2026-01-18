<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\Messenger;

/**
 * Factory for form NewMessageForm
 *
 * @author Jakub Konečný
 */
final readonly class NewMessageFormFactory
{
    public function __construct(private Messenger $model)
    {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addSelect("to", "Pro:", $this->model->usersList())
            ->setPrompt("Vyber příjemce")
            ->setRequired("Vyber příjemce.");
        $form->addText("subject", "Předmět:")
            ->addRule(Form::MAX_LENGTH, "Předmět může mít maximálně 30 znaků.", 30)
            ->setRequired("Zadej předmět.");
        $form->addTextArea("text", "Text:")
            ->setRequired("Zadej text.");
        $form->addSubmit("send", "Odeslat");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        $this->model->send($values);
    }
}
