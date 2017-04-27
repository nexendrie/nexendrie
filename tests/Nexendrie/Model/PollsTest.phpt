<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Poll;

require __DIR__ . "/../../bootstrap.php";

class PollsTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Polls */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Polls::class);
  }
  
  function testAll() {
    $result = $this->model->all();
    Assert::type(ICollection::class, $result);
    Assert::type(Poll::class, $result->fetch());
  }
  
  function testView() {
    $poll = $this->model->view(1);
    Assert::type(Poll::class, $poll);
    Assert::exception(function() {
      $this->model->view(50);
    }, PollNotFoundException::class);
  }
  
  function testExists() {
    Assert::true($this->model->exists(1));
    Assert::false($this->model->exists(50));
  }
  
  function testAdd() {
    $this->model->user = $this->getService(\Nette\Security\User::class);
    Assert::exception(function() {
      $this->model->add([]);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->add([]);
    }, MissingPermissionsException::class);
  }
  
  function testEdit() {
    $this->model->user = $this->getService(\Nette\Security\User::class);
    Assert::exception(function() {
      $this->model->edit(1, []);
    }, AuthenticationNeededException::class);
    $this->login("kazimira");
    Assert::exception(function() {
      $this->model->edit(1, []);
    }, MissingPermissionsException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->edit(50, []);
    }, PollNotFoundException::class);
    $poll = $this->model->view(1);
    $question = $poll->question;
    $this->model->edit($poll->id, ["question" => "abc"]);
    Assert::same("abc", $poll->question);
    $this->model->edit($poll->id, ["question" => $question]);
  }
}

$test = new PollsTest;
$test->run();
?>