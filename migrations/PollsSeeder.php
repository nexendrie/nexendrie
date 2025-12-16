<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class PollsSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [UserSeeder::class];
    }

    public function run(): void
    {
        $this->table("polls")
            ->insert([
                [
                    'id' => 1,
                    'question' => 'Otázka',
                    'answers' => "Možnost 1\nMožnost 2\nMožnost 3\nMožnost 4",
                    'author' => 1,
                    'created' => 1435673273,
                    'locked' => false,
                ],
                [
                    'id' => 2,
                    'question' => 'Tvé oblíbené ORM',
                    'answers' => "Doctrine\nLeanMapper\nNextras\\Orm",
                    'author' => 1,
                    'created' => 1441236118,
                    'locked' => false,
                ],
                [
                    'id' => 3,
                    'question' => 'Tvůj oblíbený framework',
                    'answers' => "Nette\nSymfony\nLaravel\nZend\nCodeIgniter",
                    'author' => 1,
                    'created' => 1444060844,
                    'locked' => false,
                ],
            ])
            ->update();
    }
}
