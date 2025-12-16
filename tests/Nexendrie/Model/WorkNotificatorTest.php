<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\UserJob;
use Nexendrie\Orm\Model as ORM;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . "/../../bootstrap.php";

/**
 * @skip
 */
class WorkNotificatorTest extends TestCase
{
    use TUserControl;

    private WorkNotificator $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(WorkNotificator::class); // @phpstan-ignore assign.propertyType
    }

    public function testNotifications(): void
    {
        Assert::exception(function () {
            $this->model->getNotifications();
        }, AuthenticationNeededException::class);

        $this->login();
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(0, $result);

        /** @var ORM $orm */
        $orm = $this->getService(ORM::class);

        $userJob = new UserJob();
        $userJob->user = $this->getUser();
        /** @var \Nexendrie\Orm\Job $job */
        $job = $orm->jobs->getById(1);
        $userJob->job = $job;
        $orm->persistAndFlush($userJob);
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(0, $result);

        $userJob->lastAction = time() - ($userJob->job->shift * 60);
        $orm->persistAndFlush($userJob);
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(1, $result);
        Assert::same(WorkNotificator::TAG_WORK_NEXT_SHIFT, $result[0]->tag);

        $userJob->lastAction = time() - ($userJob->job->shift * 60) - 1;
        $orm->persistAndFlush($userJob);
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(1, $result);
        Assert::same(WorkNotificator::TAG_WORK_NEXT_SHIFT, $result[0]->tag);

        $userJob->lastAction = time() - ($userJob->job->shift * 60) - 10;
        $orm->persistAndFlush($userJob);
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(0, $result);

        $userJob->created = time() - ($userJob->finishTime - $userJob->created);
        $orm->persistAndFlush($userJob);
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(1, $result);
        Assert::same(WorkNotificator::TAG_WORK_FINISHED, $result[0]->tag);

        $userJob->created = time() - ($userJob->finishTime - $userJob->created) - 1;
        $orm->persistAndFlush($userJob);
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(1, $result);
        Assert::same(WorkNotificator::TAG_WORK_FINISHED, $result[0]->tag);

        $userJob->created = time() - ($userJob->finishTime - $userJob->created) - 10;
        $orm->persistAndFlush($userJob);
        $result = $this->model->getNotifications();
        Assert::type("array", $result);
        Assert::count(0, $result);

        $orm->removeAndFlush($userJob);
    }
}

(new WorkNotificatorTest())->run();
