<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class UserItemSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class];
    }

    public function run(): void
    {
        $this->table("user_items")
            ->insert([
                ['id' => 1, 'item' => 9, 'user' => 1, 'amount' => 1, 'worn' => 1, 'level' => 3],
                ['id' => 2, 'item' => 13, 'user' => 1, 'amount' => 1, 'worn' => 1, 'level' => 3],
                ['id' => 3, 'item' => 14, 'user' => 1, 'amount' => 3, 'worn' => 0, 'level' => 0],
                ['id' => 4, 'item' => 13, 'user' => 3, 'amount' => 1, 'worn' => 1, 'level' => 3],
                ['id' => 5, 'item' => 18, 'user' => 1, 'amount' => 1, 'worn' => 1, 'level' => 1],
                ['id' => 6, 'item' => 13, 'user' => 2, 'amount' => 1, 'worn' => 1, 'level' => 0],
                ['id' => 7, 'item' => 17, 'user' => 2, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 8, 'item' => 13, 'user' => 4, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 9, 'item' => 24, 'user' => 4, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 10, 'item' => 2, 'user' => 4, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 11, 'item' => 22, 'user' => 2, 'amount' => 1, 'worn' => 1, 'level' => 0],
                ['id' => 12, 'item' => 24, 'user' => 3, 'amount' => 1, 'worn' => 1, 'level' => 3],
                ['id' => 13, 'item' => 18, 'user' => 4, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 14, 'item' => 18, 'user' => 2, 'amount' => 1, 'worn' => 1, 'level' => 0],
                ['id' => 15, 'item' => 18, 'user' => 3, 'amount' => 1, 'worn' => 1, 'level' => 1],
                ['id' => 16, 'item' => 35, 'user' => 4, 'amount' => 1, 'worn' => 1, 'level' => 2],
                ['id' => 17, 'item' => 38, 'user' => 4, 'amount' => 1, 'worn' => 1, 'level' => 2],
                ['id' => 18, 'item' => 41, 'user' => 4, 'amount' => 1, 'worn' => 1, 'level' => 2],
                ['id' => 19, 'item' => 44, 'user' => 1, 'amount' => 4, 'worn' => 0, 'level' => 0],
                ['id' => 20, 'item' => 25, 'user' => 3, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 21, 'item' => 1, 'user' => 3, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 22, 'item' => 25, 'user' => 1, 'amount' => 1, 'worn' => 0, 'level' => 0],
                ['id' => 23, 'item' => 3, 'user' => 1, 'amount' => 1, 'worn' => 0, 'level' => 0],
            ])
            ->update();
    }
}
