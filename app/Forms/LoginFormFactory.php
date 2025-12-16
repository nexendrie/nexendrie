<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;

/**
 * Factory for form Login
 *
 * @author Jakub Konečný
 */
final class LoginFormFactory
{
    public function __construct(private readonly User $user)
    {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addText("email", "E-mail:")
            ->setRequired("Zadej e-mail.");
        $form->addPassword("password", "Heslo:")
            ->setRequired("Zadej heslo.");
        $form->addSubmit("login", "Přihlásit se");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->user->login($values["email"], $values["password"]);
        } catch (AuthenticationException $e) {
            if ($e->getCode() === Authenticator::IDENTITY_NOT_FOUND) {
                $form->addError("Neplatný e-mail.");
            }
            if ($e->getCode() === Authenticator::INVALID_CREDENTIAL) {
                $form->addError("Neplatné heslo.");
            }
        }
    }
}
