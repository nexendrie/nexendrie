<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Guild as GuildEntity;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\User as UserEntity;
use Nexendrie\Orm\Group as GroupEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Guild Model
 *
 * @author Jakub Konečný
 * @property-read int $maxRank
 */
final class Guild
{
    use \Nette\SmartObject;

    private int $foundingPrice;

    public function __construct(
        private readonly ORM $orm,
        private readonly \Nette\Security\User $user,
        SettingsRepository $sr
    ) {
        $this->foundingPrice = $sr->settings["fees"]["foundGuild"];
    }

    /**
     * Get list of guild from specified town
     *
     * @return GuildEntity[]|ICollection
     */
    public function listOfGuilds(int $town = 0): ICollection
    {
        if ($town === 0) {
            return $this->orm->guilds->findAll();
        }
        return $this->orm->guilds->findByTown($town);
    }

    /**
     * Get specified guild
     *
     * @throws GuildNotFoundException
     */
    public function getGuild(int $id): GuildEntity
    {
        $guild = $this->orm->guilds->getById($id);
        return $guild ?? throw new GuildNotFoundException();
    }

    /**
     * Check whether a name can be used
     */
    private function checkNameAvailability(string $name, int $id = null): bool
    {
        $guild = $this->orm->guilds->getByName($name);
        return $guild === null || $guild->id === $id;
    }

    /**
     * Edit specified guild
     *
     * @throws GuildNotFoundException
     * @throws GuildNameInUseException
     */
    public function editGuild(int $id, array $data): void
    {
        $guild = $this->getGuild($id);
        foreach ($data as $key => $value) {
            if ($key === "name" && !$this->checkNameAvailability($value, $id)) {
                throw new GuildNameInUseException();
            }
            $guild->$key = $value;
        }
        $this->orm->guilds->persistAndFlush($guild);
    }

    /**
     * Get specified user's guild
     */
    public function getUserGuild(int $uid = null): ?GuildEntity
    {
        $user = $this->orm->users->getById($uid ?? $this->user->id);
        return $user?->guild;
    }

    /**
     * Check whether the user can found a guild
     */
    public function canFound(): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }
        /** @var UserEntity $user */
        $user = $this->orm->users->getById($this->user->id);
        return $user->group->path === GroupEntity::PATH_CITY && $user->guild === null;
    }

    /**
     * Found a guild
     *
     * @throws CannotFoundGuildException
     * @throws GuildNameInUseException
     * @throws InsufficientFundsException
     */
    public function found(array $data): void
    {
        if (!$this->canFound()) {
            throw new CannotFoundGuildException();
        }
        /** @var UserEntity $user */
        $user = $this->orm->users->getById($this->user->id);
        if (!$this->checkNameAvailability($data["name"])) {
            throw new GuildNameInUseException();
        }
        if ($user->money < $this->foundingPrice) {
            throw new InsufficientFundsException();
        }
        $guild = new GuildEntity();
        $this->orm->guilds->attach($guild);
        $guild->name = $data["name"];
        $guild->description = $data["description"];
        $guild->town = $this->user->identity->town;
        $user->lastActive = time();
        $user->money -= $this->foundingPrice;
        $user->guild = $guild;
        $user->guildRank = $this->getMaxRank();
        $this->orm->users->persistAndFlush($user);
    }

    /**
     * Check whether the user can join a guild
     */
    public function canJoin(): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }
        /** @var UserEntity $user */
        $user = $this->orm->users->getById($this->user->id);
        return ($user->group->path === GroupEntity::PATH_CITY && $user->guild === null);
    }

    /**
     * Join a guild
     *
     * @throws AuthenticationNeededException
     * @throws CannotJoinGuildException
     * @throws GuildNotFoundException
     */
    public function join(int $id): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        } elseif (!$this->canJoin()) {
            throw new CannotJoinGuildException();
        }
        $guild = $this->getGuild($id);
        /** @var UserEntity $user */
        $user = $this->orm->users->getById($this->user->id);
        $user->guild = $guild;
        $user->guildRank = 1;
        $this->orm->users->persistAndFlush($user);
    }

    /**
     * Check whether the user can leave guild
     * @throws AuthenticationNeededException
     */
    public function canLeave(): bool
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        /** @var UserEntity $user */
        $user = $this->orm->users->getById($this->user->id);
        if ($user->guild === null || $user->guildRank === null) {
            return false;
        }
        return !($user->guildRank->id === $this->getMaxRank());
    }

    /**
     * Leave guild
     *
     * @throws AuthenticationNeededException
     * @throws CannotLeaveGuildException
     */
    public function leave(): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        if (!$this->canLeave()) {
            throw new CannotLeaveGuildException();
        }
        /** @var UserEntity $user */
        $user = $this->orm->users->getById($this->user->id);
        $user->guild = $user->guildRank = null;
        $this->orm->users->persistAndFlush($user);
    }

    /**
     * Check whether the user can manage guild
     *
     * @throws AuthenticationNeededException
     */
    public function canManage(): bool
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        return $this->user->isAllowed(AuthorizatorFactory::GUILD_RESOURCE_NAME, "manage");
    }

    /**
     * Check whether the user can upgrade guild
     *
     * @throws AuthenticationNeededException
     */
    public function canUpgrade(): bool
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        /** @var UserEntity $user */
        $user = $this->orm->users->getById($this->user->id);
        if ($user->guild === null || !$this->user->isAllowed(AuthorizatorFactory::GUILD_RESOURCE_NAME, "upgrade")) {
            return false;
        } elseif ($user->guild->level >= GuildEntity::MAX_LEVEL) {
            return false;
        }
        return true;
    }

    /**
     * Upgrade guild
     *
     * @throws AuthenticationNeededException
     * @throws CannotUpgradeGuildException
     * @throws InsufficientFundsException
     */
    public function upgrade(): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        if (!$this->canUpgrade()) {
            throw new CannotUpgradeGuildException();
        }
        /** @var GuildEntity $guild */
        $guild = $this->getUserGuild();
        if ($guild->money < $guild->upgradePrice) {
            throw new InsufficientFundsException();
        }
        $guild->money -= $guild->upgradePrice;
        $guild->level++;
        $this->orm->guilds->persistAndFlush($guild);
    }

    /**
     * Get members of specified order
     *
     * @return UserEntity[]|ICollection
     */
    public function getMembers(int $guild): ICollection
    {
        return $this->orm->users->findByGuild($guild);
    }

    protected function getMaxRank(): int
    {
        static $rank = null;
        if ($rank === null) {
            $rank = $this->orm->guildRanks->findAll()->countStored();
        }
        return $rank;
    }

    /**
     * Promote a user
     *
     * @throws AuthenticationNeededException
     * @throws MissingPermissionsException
     * @throws UserNotFoundException
     * @throws UserNotInYourGuildException
     * @throws CannotPromoteMemberException
     */
    public function promote(int $userId): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        } elseif (!$this->user->isAllowed(AuthorizatorFactory::GUILD_RESOURCE_NAME, "promote")) {
            throw new MissingPermissionsException();
        }
        $user = $this->orm->users->getById($userId);
        if ($user === null) {
            throw new UserNotFoundException();
        }
        /** @var UserEntity $admin */
        $admin = $this->orm->users->getById($this->user->id);
        if ($admin->guild === null || $user->guild === null || $user->guild->id !== $admin->guild->id || $user->guildRank === null) {
            throw new UserNotInYourGuildException();
        } elseif ($user->guildRank->id >= $this->maxRank - 1) {
            throw new CannotPromoteMemberException();
        }
        $user->guildRank = $this->orm->guildRanks->getById($user->guildRank->id + 1);
        $this->orm->users->persistAndFlush($user);
    }

    /**
     * Demote a user
     *
     * @throws AuthenticationNeededException
     * @throws MissingPermissionsException
     * @throws UserNotFoundException
     * @throws UserNotInYourGuildException
     * @throws CannotDemoteMemberException
     */
    public function demote(int $userId): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        } elseif (!$this->user->isAllowed(AuthorizatorFactory::GUILD_RESOURCE_NAME, "demote")) {
            throw new MissingPermissionsException();
        }
        $user = $this->orm->users->getById($userId);
        if ($user === null) {
            throw new UserNotFoundException();
        }
        /** @var UserEntity $admin */
        $admin = $this->orm->users->getById($this->user->id);
        if ($admin->guild === null || $user->guild === null || $user->guild->id !== $admin->guild->id || $user->guildRank === null) {
            throw new UserNotInYourGuildException();
        } elseif ($user->guildRank->id < 2 || $user->guildRank->id === $this->maxRank) {
            throw new CannotDemoteMemberException();
        }
        $user->guildRank = $this->orm->guildRanks->getById($user->guildRank->id - 1);
        $this->orm->users->persistAndFlush($user);
    }

    /**
     * Kick a user
     *
     * @throws AuthenticationNeededException
     * @throws MissingPermissionsException
     * @throws UserNotFoundException
     * @throws UserNotInYourGuildException
     * @throws CannotKickMemberException
     */
    public function kick(int $userId): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        } elseif (!$this->user->isAllowed(AuthorizatorFactory::GUILD_RESOURCE_NAME, "kick")) {
            throw new MissingPermissionsException();
        }
        $user = $this->orm->users->getById($userId);
        if ($user === null) {
            throw new UserNotFoundException();
        }
        /** @var UserEntity $admin */
        $admin = $this->orm->users->getById($this->user->id);
        if ($admin->guild === null || $user->guild === null || $user->guild->id !== $admin->guild->id || $user->guildRank === null) {
            throw new UserNotInYourGuildException();
        } elseif ($user->guildRank->id === $this->maxRank) {
            throw new CannotKickMemberException();
        }
        $user->guild = $user->guildRank = null;
        $this->orm->users->persistAndFlush($user);
    }
}
