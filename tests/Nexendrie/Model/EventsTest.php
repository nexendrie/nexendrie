<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Collection\ICollection;
use Nexendrie\Orm\Event;

require __DIR__ . "/../../bootstrap.php";

final class EventsTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    protected Events $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(Events::class); // @phpstan-ignore assign.propertyType
    }

    public function testListOfEvents(): void
    {
        $result = $this->model->listOfEvents();
        Assert::type(ICollection::class, $result);
        Assert::type(Event::class, $result->fetch());
    }

    public function testGetEvent(): void
    {
        Assert::type(Event::class, $this->model->getEvent(1));
        Assert::exception(function () {
            $this->model->getEvent(50);
        }, EventNotFoundException::class);
    }

    public function testEditEvent(): void
    {
        Assert::exception(function () {
            $this->model->editEvent(50, []);
        }, EventNotFoundException::class);
        $event = $this->model->getEvent(1);
        $this->model->editEvent(1, ["name" => "abc"]);
        Assert::same("abc", $event->name);
        $this->model->editEvent(1, ["name" => $event->name]);
    }

    public function testDeleteEvent(): void
    {
        Assert::exception(function () {
            $this->model->deleteEvent(50);
        }, EventNotFoundException::class);
        Assert::exception(function () {
            $this->model->deleteEvent(1);
        }, CannotDeleteStartedEventException::class);
    }

    public function testCurrentEvents(): void
    {
        $events = $this->model->getCurrentEvents();
        Assert::type("array", $events);
        Assert::count(0, $events);
        Assert::same(0, $this->model->calculateAdventuresBonus(100));
        Assert::same(0, $this->model->calculateWorkBonus(100));
        Assert::same(0, $this->model->calculatePrayerLifeBonus(100));
        Assert::same(0, $this->model->calculateTrainingDiscount(100));
        Assert::same(0, $this->model->calculateShoppingDiscount(100));
        Assert::same(0, $this->model->getShoppingDiscount());
        Assert::same(0, $this->model->calculateRepairingDiscount(100));
        $event1 = new Event();
        $event2 = new Event();
        $event1->name = $event1->description = "abc";
        $event2->name = $event2->description = "def";
        $event1->start = $event2->start = 1;
        $event1->end = $event2->end = time() + 3600;
        $event1->adventuresBonus = 10;
        $event1->workBonus = $event2->workBonus = 10;
        $event1->prayerLifeBonus = 10;
        $event1->trainingDiscount = 10;
        $event1->repairingDiscount = 10;
        $event1->shoppingDiscount = 10;
        /** @var \Nexendrie\Orm\Model $orm */
        $orm = $this->getService(\Nexendrie\Orm\Model::class);
        $orm->events->persist($event1);
        $orm->events->persist($event2);
        $orm->events->flush();
        /** @var \Nette\Caching\Cache $cache */
        $cache = $this->getService(\Nette\Caching\Cache::class);
        $cache->remove("events");
        $events = $this->model->getCurrentEvents();
        Assert::type("array", $events);
        Assert::count(2, $events);
        Assert::same(10, $this->model->calculateAdventuresBonus(100));
        Assert::same(20, $this->model->calculateWorkBonus(100));
        Assert::same(10, $this->model->calculatePrayerLifeBonus(100));
        Assert::same(10, $this->model->calculateTrainingDiscount(100));
        Assert::same(10, $this->model->calculateShoppingDiscount(100));
        Assert::same(10, $this->model->getShoppingDiscount());
        Assert::same(10, $this->model->calculateRepairingDiscount(100));
        $orm->events->remove($event1);
        $orm->events->remove($event2);
        $orm->events->flush();
        $cache->remove("events");
        $events = $this->model->getCurrentEvents();
        Assert::type("array", $events);
        Assert::count(0, $events);
    }
}

$test = new EventsTest();
$test->run();
