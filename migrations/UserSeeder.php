<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class UserSeeder extends AbstractSeed {
  public function getDependencies(): array {
    return [GuildSeeder::class, OrderSeeder::class];
  }

  public function run(): void {
    $this->execute("SET foreign_key_checks = 0;");
    $this->table("users")
      ->insert([
        [
          'id' => 2,
          'publicname' => 'Rahym',
          'password' => '$2y$10$5rhQ8Puifw9YxQ8hdK.HCOeo5AW4EhLzLrDicx1TuE3TEs.tSUmVS',
          'email' => 'jakub.konecny2@seznam.cz',
          'created' => 1435240277,
          'last_active' => 1569871276,
          'last_prayer' => 1446893589,
          'last_transfer' => 1447251643,
          'group' => 4,
          'style' => 'blue-sky',
          'gender' => 'male',
          'life' => 110,
          'money' => 14357,
          'town' => 1,
          'monastery' => 2,
          'prayers' => 2,
          'guild' => null,
          'guild_rank' => null,
          'order' => null,
          'order_rank' => null,
        ],
        [
          'id' => 3,
          'publicname' => 'Jakub',
          'password' => '$2y$10$ejwYft0LbhlwhLz5vA07FOs2nNZGBb4IVpxkw7i5owXgjQ1JM6iF2',
          'email' => 'konecnyjakub01@gmail.com',
          'created' => 1441219049,
          'last_active' => 1569871410,
          'last_prayer' => null,
          'last_transfer' => 1447528739,
          'group' => 8,
          'style' => 'blue-sky',
          'gender' => 'male',
          'life' => 60,
          'money' => 20312,
          'town' => 2,
          'monastery' => null,
          'prayers' => 0,
          'guild' => 1,
          'guild_rank' => 4,
          'order' => null,
          'order_rank' => null,
        ],
        [
          'id' => 4,
          'publicname' => 'Světlana',
          'password' => '$2y$10$0a7MCizD1w6BECZvV7p4XOyA2aGyepJQPlpzJrFwvvURcSRzGpEL.',
          'email' => 'svetlana@localhost.k',
          'created' => 1455360151,
          'last_active' => 1566656429,
          'last_prayer' => 1455466667,
          'last_transfer' => 1455466659,
          'group' => 5,
          'style' => 'dark-sky',
          'gender' => 'female',
          'life' => 60,
          'money' => 14312,
          'town' => 3,
          'monastery' => null,
          'prayers' => 1,
          'guild' => null,
          'guild_rank' => null,
          'order' => 1,
          'order_rank' => 3,
        ],
        [
          'id' => 5,
          'publicname' => 'premysl',
          'password' => '$2y$10$25fvAltDnlF.TOPTj8JlK.VC2BhFmijGJNjV5HxIz1LQ9Pj0L.LQK',
          'email' => 'premysl@localhost.k',
          'created' => 1468050937,
          'last_active' => 1468050937,
          'last_prayer' => null,
          'last_transfer' => null,
          'group' => 9,
          'style' => 'blue-sky',
          'gender' => 'male',
          'life' => 60,
          'money' => 30,
          'town' => 2,
          'monastery' => null,
          'prayers' => 0,
          'guild' => null,
          'guild_rank' => null,
          'order' => null,
          'order_rank' => null,
        ],
        [
          'id' => 6,
          'publicname' => 'kazimira',
          'password' => '$2y$10$Om40QnY7ELgtedNugwwziOwVjn6mPDhFBQlXr1PR/h.w4Df0xBDZi',
          'email' => 'kazimira@localhost.k',
          'created' => 1468051028,
          'last_active' => 1475089808,
          'last_prayer' => null,
          'last_transfer' => null,
          'group' => 12,
          'style' => 'blue-sky',
          'gender' => 'female',
          'life' => 60,
          'money' => 30,
          'town' => 2,
          'monastery' => null,
          'prayers' => 0,
          'guild' => null,
          'guild_rank' => null,
          'order' => null,
          'order_rank' => null,
        ],
        [
          'id' => 7,
          'publicname' => 'bozena',
          'password' => '$2y$10$Z7McIrRNP6pOt9xDfgp7/uPTjXO933l9ky/Ns9XEUsiu6YN0v2G4S',
          'email' => 'bozena@localhost.k',
          'created' => 1495104741,
          'last_active' => 1495104741,
          'last_prayer' => null,
          'last_transfer' => null,
          'group' => 11,
          'style' => 'blue-sky',
          'gender' => 'female',
          'life' => 60,
          'money' => 30,
          'town' => 1,
          'monastery' => 2,
          'prayers' => 0,
          'guild' => null,
          'guild_rank' => null,
          'order' => null,
          'order_rank' => null,
        ],
      ])
      ->update();
    $this->table("monasteries")
      ->insert([
        [
          'id' => 2,
          'name' => 'Dům Jaly',
          'leader' => 2,
          'town' => 1,
          'created' => 1447251495,
          'money' => 0,
          'altair_level' => 6,
          'library_level' => 0,
          'hp' => 100,
        ],
      ])
      ->update();
    $this->execute("SET foreign_key_checks = 1;");
    $this->execute("UPDATE towns SET owner=4 WHERE id=10;");
    $this->execute("UPDATE users SET `order`=1, order_rank=4 WHERE id=1;");
  }
}
?>