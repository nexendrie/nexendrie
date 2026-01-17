<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Security\User;
use Nexendrie\Orm\Invitation;
use Nexendrie\Orm\Model as ORM;

final class Invitations
{
    public function __construct(private readonly ORM $orm, private readonly User $user)
    {
    }

    /**
     * @return \Nexendrie\Structs\Invitation[]
     */
    public function listOfInvitations(): array
    {
        $invitations = [];
        $entities = $this->orm->invitations->findAll();
        foreach ($entities as $entity) {
            $invitation = new \Nexendrie\Structs\Invitation();
            $invitation->email = $entity->email;
            $invitation->inviter = $entity->inviter;
            $invitation->dt = $entity->createdAt;
            $invitation->user = $this->orm->users->getByEmail($entity->email);
            $invitations[] = $invitation;
        }
        return $invitations;
    }

    public function add(string $email): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        if (!$this->user->isAllowed("user", "invite")) {
            throw new MissingPermissionsException();
        }
        if ($this->orm->invitations->getByEmail($email) !== null) {
            throw new EmailAlreadyInvitedException();
        }
        if ($this->orm->users->getByEmail($email) !== null) {
            throw new EmailAlreadyRegisteredException();
        }

        $invitation = new Invitation();
        $invitation->email = $email;
        $invitation->inviter = $this->user->id;
        $this->orm->invitations->attach($invitation);
        $this->orm->persistAndFlush($invitation);
    }

    public function remove(string $email): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        if (!$this->user->isAllowed("user", "invite")) {
            throw new MissingPermissionsException();
        }
        $invitation = $this->orm->invitations->getByEmail($email);
        if ($invitation === null) {
            throw new EmailNotInvitedException();
        }
        if ($this->orm->users->getByEmail($email) !== null) {
            throw new EmailAlreadyRegisteredException();
        }
        $this->orm->removeAndFlush($invitation);
    }

    public function isInvited(string $email): bool
    {
        return $this->orm->invitations->getByEmail($email) !== null;
    }
}
