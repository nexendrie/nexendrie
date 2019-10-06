<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class BeerProductionSeeder extends AbstractSeed {
  public function getDependencies(): array {
    return [UserSeeder::class, HouseSeeder::class];
  }

  public function run(): void {
    $this->table("beer_production")
      ->insert([
        ['id' => 1, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1449771650],
        ['id' => 2, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1450376603],
        ['id' => 3, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1450706268],
        ['id' => 4, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1452105091],
        ['id' => 5, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1452719105],
        ['id' => 6, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1453401603],
        ['id' => 7, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1454834066],
        ['id' => 8, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1455448359],
        ['id' => 9, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1456064093],
        ['id' => 10, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1457266437],
        ['id' => 11, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1457883895],
        ['id' => 12, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1458489138],
        ['id' => 13, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1459097957],
        ['id' => 14, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1460798232],
        ['id' => 15, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1461601500],
        ['id' => 16, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1462464687],
        ['id' => 17, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1463835106],
        ['id' => 18, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1464511812],
        ['id' => 19, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1465117203],
        ['id' => 20, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1465731299],
        ['id' => 21, 'user' => 3, 'house' => 1, 'amount' => 1, 'price' => 30, 'when' => 1466337743],
        ['id' => 22, 'user' => 3, 'house' => 1, 'amount' => 2, 'price' => 30, 'when' => 1466943222],
        ['id' => 23, 'user' => 3, 'house' => 1, 'amount' => 2, 'price' => 30, 'when' => 1467552000],
        ['id' => 24, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1468175438],
        ['id' => 25, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1469559471],
        ['id' => 26, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1470470909],
        ['id' => 27, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1471633090],
        ['id' => 28, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1472284608],
        ['id' => 29, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1473220001],
        ['id' => 30, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1473862866],
        ['id' => 31, 'user' => 3, 'house' => 1, 'amount' => 5, 'price' => 30, 'when' => 1474467761],
      ])
      ->update();
  }
}
?>