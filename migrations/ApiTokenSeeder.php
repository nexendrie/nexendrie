<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ApiTokenSeeder extends AbstractSeed {
  public function run(): void {
    $this->table("api_tokens")
      ->insert([
        [
          'id' => 1,
          'token' => 'test1',
          'user' => 1,
          'created' => time(),
          'expire' => time() + 3600,
        ],
        [
          'id' => 2,
          'token' => 'test2',
          'user' => 1,
          'created' => time(),
          'expire' => time() + 3600,
        ],
        [
          'id' => 3,
          'token' => 'test3',
          'user' => 2,
          'created' => time(),
          'expire' => time() + 3600,
        ],
      ])
      ->update();
  }
}
?>