<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\UserManager;
use Nexendrie\Model\RegistrationException;

/**
 * Factory for form Register
 *
 * @author Jakub Konečný
 */
final readonly class RegisterFormFactory
{
    public function __construct(private UserManager $model)
    {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addText("publicname", "Jméno:")
            ->addRule(Form::MAX_LENGTH, "Jméno může mít maximálně 25 znaků.", 25)
            ->setRequired("Zadej jméno.")
            ->setOption("description", "Toto jméno se zobrazuje ostatním.");
        $form->addEmail("email", "E-mail:")
            ->setRequired("Zadej e-mail.")
            ->setOption("description", "Slouží jako uživatelské jméno.");
        $form->addPassword("password", "Heslo:")
            ->setRequired("Zadej heslo.");
        $form->addSubmit("register", "Zaregistrovat se");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->model->register($values);
        } catch (RegistrationException $e) {
            if ($e->getCode() === UserManager::REG_DUPLICATE_EMAIL) {
                $form->addError("Zadaný e-mail je už používán.");
            }
            if ($e->getCode() === UserManager::REG_DUPLICATE_NAME) {
                $form->addError("Zvolené jméno je už zabráno.");
            }
            if ($e->getCode() === UserManager::REG_EMAIL_NOT_INVITED) {
                $form->addError("Nemáš platnou pozvánku.");
            }
        }
    }
}
