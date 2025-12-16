<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class CommentSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class, ArticleSeeder::class];
    }

    public function run(): void
    {
        $this->table("comments")
            ->insert([
                [
                    'id' => 1,
                    'title' => 'Test',
                    'text' => 'komentář',
                    'article' => 1,
                    'author' => 1,
                    'created' => 1435085477,
                ],
                [
                    'id' => 2,
                    'title' => 'Test',
                    'text' => 'test test test test test',
                    'article' => 2,
                    'author' => 1,
                    'created' => 1435250522,
                ],
                [
                    'id' => 3,
                    'title' => 'Test 2',
                    'text' => 'text',
                    'article' => 2,
                    'author' => 1,
                    'created' => 1441223636,
                ],
                [
                    'id' => 4,
                    'title' => 'Hotovo',
                    'text' => 'Přechod již byl úspěšně dokončen.',
                    'article' => 3,
                    'author' => 1,
                    'created' => 1441372474,
                ],
                [
                    'id' => 5,
                    'title' => 'Test',
                    'text' => 'text',
                    'article' => 6,
                    'author' => 1,
                    'created' => 1445197436,
                ],
            ])
            ->update();
    }
}
