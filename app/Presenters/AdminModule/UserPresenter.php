<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Nexendrie\Forms\EditUserFormFactory;
use Nexendrie\Forms\BanUserFormFactory;
use Nette\Application\UI\Form;
use Nexendrie\Forms\InviteUserFormFactory;
use Nexendrie\Model\EmailAlreadyRegisteredException;
use Nexendrie\Model\EmailNotInvitedException;
use Nexendrie\Model\Invitations;
use Nexendrie\Model\UserManager;
use Nexendrie\Model\UserNotFoundException;

/**
 * Presenter User
 *
 * @author Jakub Konečný
 */
final class UserPresenter extends BasePresenter
{
    public function __construct(private readonly UserManager $model, private readonly Invitations $invitations)
    {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $this->requiresPermissions("user", "list");
        $this->template->users = $this->model->listOfUsers();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit(int $id): void
    {
        $this->requiresPermissions("user", "edit");
        try {
            $this->model->get($id);
        } catch (UserNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function createComponentEditUser(EditUserFormFactory $factory): Form
    {
        $form = $factory->create((int) $this->getParameter("id"));
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Změny uloženy.");
            $this->redirect("default");
        };
        return $form;
    }

    public function actionBan(int $id): void
    {
        $this->requiresPermissions("user", "ban");
        if ($id === 0) {
            $this->flashMessage("Neoprávněná operace.");
            $this->redirect("default");
        }
        try {
            $user = $this->model->get($id);
            if ($user->group->id === 1) {
                $this->flashMessage("Neoprávněná operace.");
                $this->redirect("default");
            } elseif ($user->banned) {
                $this->flashMessage("Uživatel už je uvězněn.");
                $this->redirect("default");
            }
        } catch (UserNotFoundException $e) {
            $this->flashMessage("Zadaný uživatel neexistuje.");
            $this->redirect("default");
        }
        $this->template->name = $user->publicname;
    }

    protected function createComponentBanUserForm(BanUserFormFactory $factory): Form
    {
        $form = $factory->create((int) $this->getParameter("id"));
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Uživatel uvězněn.");
        };
        return $form;
    }

    public function actionInvitations(): void
    {
        $this->requiresPermissions("user", "invite");
        $this->template->invitations = $this->invitations->listOfInvitations();
    }

    protected function createComponentInviteUserForm(InviteUserFormFactory $factory): Form
    {
        $form = $factory->create();
        $form->onSuccess[] = function (): void {
            $this->flashMessage("Pozvánka vytvořena.");
            $this->redirect("this");
        };
        return $form;
    }

    public function handleCancelInvitation(string $email): never
    {
        $this->requiresPermissions("user", "invite");
        try {
            $this->invitations->remove($email);
            $this->flashMessage("Pozvánka zrušena.");
        } catch (EmailNotInvitedException) {
            $this->flashMessage("Neexistuje pozvánka pro danou e-mailovou adresu.");
        } catch (EmailAlreadyRegisteredException) {
            $this->flashMessage("Už existuje uživatel s danou e-mailovou adresou.");
        }
        $this->redirect("this");
    }
}
