<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Structs\Invitation;
use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class InvitationsTest extends \Tester\TestCase
{
    use TUserControl;

    protected Invitations $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(Invitations::class); // @phpstan-ignore assign.propertyType
    }

    public function testProcess(): void
    {
        $email = "abc@def";
        Assert::count(0, $this->model->listOfInvitations());
        Assert::false($this->model->isInvited($email));
        Assert::exception(function () {
            $this->model->add("abc");
        }, AuthenticationNeededException::class);
        $this->login("Jakub");
        Assert::exception(function () {
            $this->model->add("abc");
        }, MissingPermissionsException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->add($this->getUserStat("email"));
        }, EmailAlreadyRegisteredException::class);
        $this->model->add($email);
        Assert::exception(function () use ($email) {
            $this->model->add($email);
        }, EmailAlreadyInvitedException::class);
        Assert::true($this->model->isInvited($email));
        $invitations = $this->model->listOfInvitations();
        Assert::count(1, $invitations);
        Assert::type(Invitation::class, $invitations[0]);
        Assert::same($email, $invitations[0]->email);
        Assert::same($this->getUserStat("id"), $invitations[0]->inviter->id);
        Assert::null($invitations[0]->user);
        $this->logout();
        Assert::exception(function () {
            $this->model->remove("abc");
        }, AuthenticationNeededException::class);
        $this->login("Jakub");
        Assert::exception(function () {
            $this->model->remove("abc");
        }, MissingPermissionsException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->remove("abc");
        }, EmailNotInvitedException::class);
        $this->model->remove($email);
        Assert::count(0, $this->model->listOfInvitations());
        Assert::false($this->model->isInvited($email));
    }
}

$test = new InvitationsTest();
$test->run();
