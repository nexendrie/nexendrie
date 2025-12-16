<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class OrderSeeder extends AbstractSeed
{
    public function run(): void
    {
        $this->table("orders")
            ->insert([
                [
                    'id' => 1,
                    'name' => 'Řád dračích jezdců',
                    'description' => '.',
                    'level' => 2,
                    'created' => 1465120352,
                    'money' => 400,
                ],
            ])
            ->update();
    }
}
