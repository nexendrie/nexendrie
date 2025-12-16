<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class MarriageSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class];
    }

    public function run(): void
    {
        $this->table("marriages")
            ->insert([
                [
                    'id' => 1,
                    'user1' => 4,
                    'user2' => 1,
                    'status' => 'active',
                    'divorce' => 0,
                    'created' => 1466241338,
                    'accepted' => 1466245558,
                    'term' => 1467450938,
                    'cancelled' => null,
                    'intimacy' => 5,
                ],
                [
                    'id' => 2,
                    'user1' => 3,
                    'user2' => 6,
                    'status' => 'accepted',
                    'divorce' => 0,
                    'created' => 1475264924,
                    'accepted' => 1475264945,
                    'term' => 1792007340,
                    'cancelled' => null,
                    'intimacy' => 0,
                ],
            ])
            ->update();
    }
}
