<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class UserSkillSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class];
    }

    public function run(): void
    {
        $this->table("user_skills")
            ->insert([
                ['id' => 1, 'user' => 1, 'skill' => 3, 'level' => 5],
                ['id' => 2, 'user' => 1, 'skill' => 6, 'level' => 5],
                ['id' => 3, 'user' => 2, 'skill' => 1, 'level' => 5],
                ['id' => 4, 'user' => 2, 'skill' => 6, 'level' => 5],
                ['id' => 5, 'user' => 3, 'skill' => 1, 'level' => 5],
                ['id' => 6, 'user' => 3, 'skill' => 6, 'level' => 5],
                ['id' => 7, 'user' => 4, 'skill' => 5, 'level' => 5],
                ['id' => 8, 'user' => 1, 'skill' => 7, 'level' => 10],
            ])
            ->update();
    }
}
