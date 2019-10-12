<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class ChatMessageSeeder extends AbstractSeed {
  public function getDependencies(): array {
    return [UserSeeder::class, GuildSeeder::class, OrderSeeder::class];
  }

  public function run(): void {
    $this->table("chat_messages")
      ->insert([
        [
          'id' => 1,
          'message' => 'Vítejte v cechu',
          'created' => 1521573723,
          'user' => 3,
          'town' => null,
          'monastery' => null,
          'guild' => 1,
          'order' => null,
        ],
      ])
      ->update();
  }
}
?>