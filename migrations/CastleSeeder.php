<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class CastleSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class];
    }

    public function run(): void
    {
        $this->table("castles")
            ->insert([
                [
                    'id' => 2,
                    'name' => 'Falver',
                    'description' => '.',
                    'created' => 1447420077,
                    'owner' => 1,
                    'level' => 5,
                    'hp' => '100',
                ],
                [
                    'id' => 3,
                    'name' => 'Erdvor',
                    'description' => '.',
                    'created' => 1466869822,
                    'owner' => 4,
                    'level' => 3,
                    'hp' => '100',
                ],
            ])
            ->update();
    }
}
