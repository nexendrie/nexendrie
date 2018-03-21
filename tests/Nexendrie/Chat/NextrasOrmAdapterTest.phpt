<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

final class NextrasOrmAdapterTest extends \Tester\TestCase {
  use \Nexendrie\Model\TUserControl;
  
  /** @var NextrasOrmAdapter */
  protected $model;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  protected function setUp() {
    $this->model = $this->getService(NextrasOrmAdapter::class);
    $this->orm = $this->getService(\Nexendrie\Orm\Model::class);
  }
  
  public function testGetTexts() {
    $texts = $this->model->getTexts("guild", 1, 25);
    Assert::type(ChatMessagesCollection::class, $texts);
    Assert::count(1, $texts);
  }
  
  public function testGetCharacters() {
    $this->login();
    $characters = $this->model->getCharacters("town", 2);
    Assert::type(ChatCharactersCollection::class, $characters);
  }
  
  public function testAddMessage() {
    $texts = $this->model->getTexts("town", 2, 25);
    Assert::count(0, $texts);
    $this->login();
    $this->model->addMessage("test", "town", 2);
    $texts = $this->model->getTexts("town", 2, 25);
    Assert::count(1, $texts);
    $this->orm->chatMessages->removeAndFlush($this->orm->chatMessages->getById($texts[0]->id));
  }
}

$test = new NextrasOrmAdapterTest();
$test->run();
?>