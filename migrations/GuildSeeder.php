<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class GuildSeeder extends AbstractSeed {
  public function run(): void {
    $this->table("guilds")
      ->insert([
        [
          'id' => 1,
          'name' => 'Cech kupců z Myhru',
          'description' => '.',
          'level' => 2,
          'created' => 1453484840,
          'town' => 2,
          'money' => 300,
          'skill' => 6,
        ],
      ])
      ->update();
  }
}
?>