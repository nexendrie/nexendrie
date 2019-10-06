<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class PunishmentSeeder extends AbstractSeed {
  public function getDependencies(): array {
    return [UserSeeder::class];
  }

  public function run(): void {
    $this->table("punishments")
      ->insert([
        [
          'id' => 1,
          'user' => 2,
          'crime' => 'zlobil',
          'imprisoned' => 1445172553,
          'released' => 1445179141,
          'number_of_shifts' => 5,
          'count' => 5,
          'last_action' => 1445178236,
        ],
      ])
      ->update();
  }
}
?>