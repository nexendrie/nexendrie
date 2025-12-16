<?php
declare(strict_types=1);

namespace Nexendrie\Forms;

use Nette\Application\UI\Form;
use Nexendrie\Model\EmailAlreadyInvitedException;
use Nexendrie\Model\EmailAlreadyRegisteredException;
use Nexendrie\Model\Invitations;

final class InviteUserFormFactory
{
    public function __construct(private readonly Invitations $invitations)
    {
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addEmail("email", "E-mail")
            ->setRequired("Zadej e-mail.");
        $form->addSubmit("invite", "Vytvořit pozvánku");
        $form->onSuccess[] = $this->process(...);
        return $form;
    }

    public function process(Form $form, array $values): void
    {
        try {
            $this->invitations->add($values["email"]);
        } catch (EmailAlreadyInvitedException) {
            $form->addError("Už existuje pozvánka pro danou e-mailovou adresu.");
        } catch (EmailAlreadyRegisteredException) {
            $form->addError("Už existuje uživatel s danou e-mailovou adresou.");
        }
    }
}
