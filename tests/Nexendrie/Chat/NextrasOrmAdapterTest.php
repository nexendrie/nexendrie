<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;
use HeroesofAbenez\Chat\ChatMessagesCollection;
use HeroesofAbenez\Chat\ChatCharactersCollection;

final class NextrasOrmAdapterTest extends \Tester\TestCase
{
    use \Nexendrie\Model\TUserControl;

    protected NextrasOrmAdapter $model;
    protected \Nexendrie\Orm\Model $orm;

    protected function setUp(): void
    {
        $this->model = $this->getService(NextrasOrmAdapter::class); // @phpstan-ignore assign.propertyType
        $this->orm = $this->getService(\Nexendrie\Orm\Model::class); // @phpstan-ignore assign.propertyType
    }

    public function testGetTexts(): void
    {
        $texts = $this->model->getTexts("guild", 1, 25);
        Assert::type(ChatMessagesCollection::class, $texts);
        Assert::count(1, $texts);
    }

    public function testGetCharacters(): void
    {
        $this->login();
        $characters = $this->model->getCharacters("town", 2);
        Assert::type(ChatCharactersCollection::class, $characters);
    }

    public function testAddMessage(): void
    {
        $texts = $this->model->getTexts("town", 2, 25);
        Assert::count(0, $texts);
        $this->login();
        $this->model->addMessage("test", "town", 2);
        $texts = $this->model->getTexts("town", 2, 25);
        Assert::count(1, $texts);
        // @phpstan-ignore property.notFound, argument.type
        $this->orm->chatMessages->removeAndFlush($this->orm->chatMessages->getById($texts[0]->id));
    }
}

$test = new NextrasOrmAdapterTest();
$test->run();
