<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Marriage as MarriageEntity;

require __DIR__ . "/../../bootstrap.php";


class MarriageTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Marriage */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Marriage::class);
  }
  
  function testListOfMarriages() {
    $result = $this->model->listOfMarriages();
    Assert::type(ICollection::class, $result);
    Assert::type(MarriageEntity::class, $result->fetch());
  }
  
  function testGetMarriage() {
    $marriage = $this->model->getMarriage(1);
    Assert::type(MarriageEntity::class, $marriage);
    Assert::exception(function() {
      $this->model->getMarriage(50);
    }, MarriageNotFoundException::class);
  }
  
  function testCanPropose() {
    Assert::false($this->model->canPropose(1));
    $this->login();
    Assert::false($this->model->canPropose(1));
    Assert::false($this->model->canPropose(0));
    Assert::false($this->model->canPropose(50));
    Assert::false($this->model->canPropose(2));
    $this->login("kazimira");
    Assert::false($this->model->canPropose(1));
    $this->login("Rahym");
    Assert::false($this->model->canPropose(0));
  }
  
  /**
   *
   */
  function testCanFinish() {
    /** @var \Nexendrie\Orm\Model $orm */
    //$orm = $this->getService(\Nexendrie\Orm\Model::class);
    $marriage1 = $this->model->getMarriage(1);
    $marriage2 = $this->model->getMarriage(2);
    Assert::false($this->model->canFinish($marriage1));
    /*$marriage = new MarriageEntity;
    $marriage->status = MarriageEntity::STATUS_ACCEPTED;
    $marriage->proposed = time();
    $orm->marriages->attach($marriage);
    $marriage->user1 = 1;
    $marriage->user2 = 0;
    Assert::false($this->model->canFinish($marriage));
    $orm->marriages->detach($marriage);*/
    Assert::true($this->model->canFinish($marriage2));
  }
  
  function testListOfProposals() {
    Assert::exception(function() {
      $this->model->listOfProposals();
    }, AuthenticationNeededException::class);
    $this->login();
    $result = $this->model->listOfProposals();
    Assert::type(ICollection::class, $result);
  }
  
  function testAcceptProposal() {
    Assert::exception(function() {
      $this->model->acceptProposal(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->acceptProposal(50);
    }, MarriageNotFoundException::class);
    Assert::exception(function() {
      $this->model->acceptProposal(2);
    }, AccessDeniedException::class);
    Assert::exception(function() {
      $this->model->acceptProposal(1);
    }, MarriageProposalAlreadyHandledException::class);
  }
  
  function testDeclineProposal() {
    Assert::exception(function() {
      $this->model->declineProposal(1);
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->declineProposal(50);
    }, MarriageNotFoundException::class);
    Assert::exception(function() {
      $this->model->declineProposal(2);
    }, AccessDeniedException::class);
    Assert::exception(function() {
      $this->model->declineProposal(1);
    }, MarriageProposalAlreadyHandledException::class);
  }
  
  function testGetCurrentMarriage() {
    Assert::exception(function() {
      $this->model->getCurrentMarriage();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::type(MarriageEntity::class, $this->model->getCurrentMarriage());
    $this->login("jakub");
    Assert::type(MarriageEntity::class, $this->model->getCurrentMarriage());
    $this->login("system");
    Assert::null($this->model->getCurrentMarriage());
  }
  
  function testCancelWedding() {
    Assert::exception(function() {
      $this->model->cancelWedding();
    }, AuthenticationNeededException::class);
    $this->login("system");
    Assert::exception(function() {
      $this->model->cancelWedding();
    }, NotEngagedException::class);
  }
}

$test = new MarriageTest;
$test->run();
?>