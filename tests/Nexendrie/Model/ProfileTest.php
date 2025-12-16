<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Orm\User as UserEntity;
use Nexendrie\Orm\Group;

require __DIR__ . "/../../bootstrap.php";

final class ProfileTest extends \Tester\TestCase
{
    use \Testbench\TCompiledContainer;

    protected Profile $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(Profile::class); // @phpstan-ignore assign.propertyType
    }

    public function testView(): void
    {
        $user = $this->model->view("Vladěna");
        Assert::type(UserEntity::class, $user);
        Assert::exception(function () {
            $this->model->view("a");
        }, UserNotFoundException::class);
    }

    public function testGetListOfLords(): void
    {
        $result = $this->model->getListOfLords();
        Assert::type("array", $result);
        Assert::count(3, $result);
        foreach ($result as $key => $value) {
            Assert::type("int", $key);
            Assert::type("string", $value);
        }
    }

    public function testGetPath(): void
    {
        Assert::same(Group::PATH_TOWER, $this->model->getPath(1));
        Assert::same(Group::PATH_CHURCH, $this->model->getPath(2));
        Assert::same(Group::PATH_CITY, $this->model->getPath(3));
        Assert::exception(function () {
            $this->model->getPath(50);
        }, UserNotFoundException::class);
    }

    public function testGetPartner(): void
    {
        /** @var UserEntity $partner1 */
        $partner1 = $this->model->getPartner(4);
        Assert::type(UserEntity::class, $partner1);
        Assert::same(1, $partner1->id);
        /** @var UserEntity $partner2 */
        $partner2 = $this->model->getPartner(1);
        Assert::type(UserEntity::class, $partner2);
        Assert::same(4, $partner2->id);
        Assert::null($this->model->getPartner(2));
    }

    public function testGetFiance(): void
    {
        /** @var UserEntity $partner1 */
        $partner1 = $this->model->getFiance(3);
        Assert::type(UserEntity::class, $partner1);
        Assert::same(6, $partner1->id);
        /** @var UserEntity $partner2 */
        $partner2 = $this->model->getFiance(6);
        Assert::type(UserEntity::class, $partner2);
        Assert::same(3, $partner2->id);
        Assert::null($this->model->getFiance(2));
    }

    public function testGetArticles(): void
    {
        $articles = $this->model->getArticles("Trimadyl z Myhru");
        Assert::type(OneHasMany::class, $articles);
        Assert::count(16, $articles);
        Assert::exception(function () {
            $this->model->getArticles("abc");
        }, UserNotFoundException::class);
    }

    public function testGetSkills(): void
    {
        $skills = $this->model->getSkills("Rahym");
        Assert::type(OneHasMany::class, $skills);
        Assert::count(2, $skills);
        Assert::exception(function () {
            $this->model->getSkills("abc");
        }, UserNotFoundException::class);
    }
}

$test = new ProfileTest();
$test->run();
