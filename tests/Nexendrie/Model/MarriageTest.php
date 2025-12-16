<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Marriage as MarriageEntity;

require __DIR__ . "/../../bootstrap.php";


final class MarriageTest extends \Tester\TestCase
{
    use TUserControl;

    protected Marriage $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(Marriage::class); // @phpstan-ignore assign.propertyType
    }

    public function testListOfMarriages(): void
    {
        $result = $this->model->listOfMarriages();
        Assert::type(ICollection::class, $result);
        Assert::type(MarriageEntity::class, $result->fetch());
    }

    public function testGetMarriage(): void
    {
        $marriage = $this->model->getMarriage(1);
        Assert::type(MarriageEntity::class, $marriage);
        Assert::exception(function () {
            $this->model->getMarriage(50);
        }, MarriageNotFoundException::class);
    }

    public function testCanPropose(): void
    {
        Assert::false($this->model->canPropose(1));
        $this->login();
        Assert::false($this->model->canPropose(1));
        Assert::false($this->model->canPropose(0));
        Assert::false($this->model->canPropose(50));
        Assert::false($this->model->canPropose(2));
        $this->login("kazimira");
        Assert::false($this->model->canPropose(1));
        Assert::false($this->model->canPropose(5));
        $this->login("Rahym");
        Assert::false($this->model->canPropose(0));
        $this->login("premysl");
        Assert::false($this->model->canPropose(3));
        Assert::false($this->model->canPropose(7));
    }

    public function testCanFinish(): void
    {
        $marriage1 = $this->model->getMarriage(1);
        $marriage2 = $this->model->getMarriage(2);
        Assert::false($this->model->canFinish($marriage1));
        Assert::true($this->model->canFinish($marriage2));
        /** @var \Nexendrie\Orm\Model $orm */
        $orm = $this->getService(\Nexendrie\Orm\Model::class);
        $marriage = new MarriageEntity();
        $marriage->status = MarriageEntity::STATUS_ACCEPTED;
        $marriage->created = time();
        $marriage->user1 = $orm->users->getById(1); // @phpstan-ignore assign.propertyType
        $marriage->user2 = $orm->users->getById(0); // @phpstan-ignore assign.propertyType
        Assert::false($this->model->canFinish($marriage));
        $orm->marriages->removeAndFlush($marriage);
    }

    public function testListOfProposals(): void
    {
        Assert::exception(function () {
            $this->model->listOfProposals();
        }, AuthenticationNeededException::class);
        $this->login();
        $result = $this->model->listOfProposals();
        Assert::type(ICollection::class, $result);
    }

    public function testAcceptProposal(): void
    {
        Assert::exception(function () {
            $this->model->acceptProposal(1);
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->acceptProposal(50);
        }, MarriageNotFoundException::class);
        Assert::exception(function () {
            $this->model->acceptProposal(2);
        }, AccessDeniedException::class);
        Assert::exception(function () {
            $this->model->acceptProposal(1);
        }, MarriageProposalAlreadyHandledException::class);
    }

    public function testDeclineProposal(): void
    {
        Assert::exception(function () {
            $this->model->declineProposal(1);
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->declineProposal(50);
        }, MarriageNotFoundException::class);
        Assert::exception(function () {
            $this->model->declineProposal(2);
        }, AccessDeniedException::class);
        Assert::exception(function () {
            $this->model->declineProposal(1);
        }, MarriageProposalAlreadyHandledException::class);
    }

    public function testGetCurrentMarriage(): void
    {
        Assert::exception(function () {
            $this->model->getCurrentMarriage();
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::type(MarriageEntity::class, $this->model->getCurrentMarriage());
        $this->login("Jakub");
        Assert::type(MarriageEntity::class, $this->model->getCurrentMarriage());
        $this->login("Vladěna");
        Assert::null($this->model->getCurrentMarriage());
    }

    public function testCancelWedding(): void
    {
        Assert::exception(function () {
            $this->model->cancelWedding();
        }, AuthenticationNeededException::class);
        $this->login("Vladěna");
        Assert::exception(function () {
            $this->model->cancelWedding();
        }, NotEngagedException::class);
    }
}

$test = new MarriageTest();
$test->run();
