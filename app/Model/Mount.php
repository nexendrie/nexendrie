<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Mount as MountEntity;
use Nexendrie\Orm\MountType as MountTypeEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Mount Model
 *
 * @author Jakub Konečný
 */
final class Mount
{
    public function __construct(private readonly ORM $orm, private readonly \Nette\Security\User $user)
    {
    }

    /**
     * Get specified mount
     *
     * @throws MountNotFoundException
     */
    public function get(int $id): MountEntity
    {
        $mount = $this->orm->mounts->getById($id);
        return $mount ?? throw new MountNotFoundException();
    }

    /**
     * Get list of all mounts
     *
     * @param int|null $owner Return only mounts owned by specified user. null = all users
     * @return MountEntity[]|ICollection
     */
    public function listOfMounts(int $owner = null): ICollection
    {
        if (is_int($owner)) {
            return $this->orm->mounts->findByOwner($owner);
        }
        return $this->orm->mounts->findAll();
    }

    /**
     * Get mounts on sale
     *
     * @return MountEntity[]|ICollection
     */
    public function mountsOnSale(): ICollection
    {
        return $this->orm->mounts->findOnMarket()
            ->orderBy("type->id", ICollection::DESC)
            ->orderBy("price", ICollection::DESC);
    }

    /**
     * Get list of all mount types
     *
     * @return MountTypeEntity[]|ICollection
     */
    public function listOfMountTypes(): ICollection
    {
        return $this->orm->mountTypes->findAll();
    }

    /**
     * Add new mount
     */
    public function add(array $data): void
    {
        $mount = new MountEntity();
        $this->orm->mounts->attach($mount);
        foreach ($data as $key => $value) {
            $mount->$key = $value;
        }
        $mount->owner = 0;
        $this->orm->mounts->persistAndFlush($mount);
    }

    /**
     * Edit specified mount
     *
     * @throws MountNotFoundException
     */
    public function edit(int $id, array $data): void
    {
        try {
            $mount = $this->get($id);
        } catch (MountNotFoundException $e) {
            throw $e;
        }
        foreach ($data as $key => $value) {
            $mount->$key = $value;
        }
        $this->orm->mounts->persistAndFlush($mount);
    }

    /**
     * Buy specified mount
     *
     * @throws AuthenticationNeededException
     * @throws MountNotFoundException
     * @throws MountNotOnSaleException
     * @throws CannotBuyOwnMountException
     * @throws InsufficientLevelForMountException
     * @throws InsufficientFundsException
     */
    public function buy(int $id): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        try {
            $mount = $this->get($id);
        } catch (MountNotFoundException $e) {
            throw $e;
        }
        if (!$mount->onMarket) {
            throw new MountNotOnSaleException();
        }
        if ($mount->owner->id === $this->user->id) {
            throw new CannotBuyOwnMountException();
        }
        /** @var \Nexendrie\Orm\User $user */
        $user = $this->orm->users->getById($this->user->id);
        if ($user->group->level < $mount->type->level) {
            throw new InsufficientLevelForMountException();
        }
        if ($user->money < $mount->price) {
            throw new InsufficientFundsException();
        }
        $seller = $mount->owner;
        $seller->money += $mount->price;
        $this->orm->users->persist($seller);
        $user->money -= $mount->price;
        $user->lastActive = time();
        $mount->owner = $user;
        $mount->onMarket = false;
        $this->orm->mounts->persist($mount);
        $this->orm->flush();
    }
}
