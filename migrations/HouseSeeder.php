<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class HouseSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class];
    }

    public function run(): void
    {
        $this->table("houses")
            ->insert([['id' => 1, 'owner' => 3, 'luxury_level' => 5, 'brewery_level' => 5, 'hp' => 100]])
            ->update();
    }
}
