<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class EventSeeder extends AbstractSeed {
  public function run(): void {
    $this->table("events")
      ->insert([
        [
          'id' => 1,
          'name' => 'Oslavy založení',
          'description' => 'd',
          'start' => 1467756000,
          'end' => 1468360740,
          'adventures_bonus' => 50,
          'work_bonus' => 50,
          'prayer_life_bonus' => 50,
          'training_discount' => 50,
          'repairing_discount' => 50,
          'shopping_discount' => 50,
        ],
      ])
      ->update();
  }
}
?>