<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Orm\Guild;
use Nexendrie\Orm\GuildFee;
use Nexendrie\Orm\GuildRank;
use Nexendrie\Orm\Model as ORM;

/**
 * GuildFeesTask
 *
 * @author Jakub Konečný
 */
final class GuildFeesTask extends BaseMonthlyCronTask
{
    public function __construct(private readonly ORM $orm)
    {
    }

    private function getFeeRecord(\Nexendrie\Orm\User $user): GuildFee
    {
        /** @var Guild $guild */
        $guild = $user->guild;
        $fee = $this->orm->guildFees->getByUserAndGuild($user, $guild);
        if ($fee !== null) {
            return $fee;
        }
        $fee = new GuildFee();
        $fee->user = $user;
        $fee->guild = $guild;
        return $fee;
    }

    /**
     * @cronner-task(Guild fees)
     * @cronner-period(1 day)
     * @cronner-time(00:00 - 01:00)
     */
    public function run(): void
    {
        $date = new \DateTime();
        $date->setTimestamp(time());
        if (!$this->isDue($date)) {
            return;
        }
        echo "Starting paying guild fees ...\n";
        $users = $this->orm->users->findInGuild();
        foreach ($users as $user) {
            /** @var Guild $guild */
            $guild = $user->guild;
            /** @var GuildRank $guildRank */
            $guildRank = $user->guildRank;
            $guildFee = $guildRank->guildFee;
            echo "$user->publicname (#$user->id} will pay {$guildFee} to his/her guild.\n";
            $user->money -= $guildFee;
            $guild->money += $guildFee;
            $this->orm->users->persist($user);
            $fee = $this->getFeeRecord($user);
            $fee->amount += $guildFee;
            $this->orm->guildFees->persistAndFlush($fee);
        }
        echo "Finished paying guild fees ...\n";
    }
}
