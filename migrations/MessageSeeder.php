<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class MessageSeeder extends AbstractSeed {
  public function getDependencies(): array {
    return [UserSeeder::class];
  }

  public function run(): void {
    $this->table("messages")
      ->insert([
        [
          'id' => 1,
          'subject' => 'Test',
          'text' => 'Test message.',
          'from' => 2,
          'to' => 1,
          'created' => 1434731668,
          'read' => true,
        ],
        [
          'id' => 2,
          'subject' => 'Test',
          'text' => 'Test message.',
          'from' => 1,
          'to' => 2,
          'created' => 1434731668,
          'read' => true,
        ],
        [
          'id' => 3,
          'subject' => 'Zpráva',
          'text' => 'text text text',
          'from' => 1,
          'to' => 2,
          'created' => 1434904922,
          'read' => true,
        ],
        [
          'id' => 4,
          'subject' => 'Orm',
          'text' => 'Lorem ipsum dota',
          'from' => 1,
          'to' => 3,
          'created' => 1441278929,
          'read' => false,
        ],
        [
          'id' => 5,
          'subject' => 'Test',
          'text' => 'Just a test.',
          'from' => 1,
          'to' => 3,
          'created' => 1441307001,
          'read' => false,
        ],
        [
          'id' => 6,
          'subject' => 'Test',
          'text' => 'tttttest',
          'from' => 1,
          'to' => 3,
          'created' => 1444060591,
          'read' => false,
        ],
        [
          'id' => 7,
          'subject' => 'Povýšení',
          'text' => 'Již nějakou dobu jsi řádným občanem Nexendrie a proto jsi byl povýšen na Měšťana.',
          'from' => 1,
          'to' => 3,
          'created' => 1447529598,
          'read' => false,
        ],
        [
          'id' => 8,
          'subject' => 'Dárek',
          'text' => 'Dostal jsi 1000 grošů a  Právo na založení města.',
          'from' => 1,
          'to' => 1,
          'created' => 1447595907,
          'read' => true,
        ],
        [
          'id' => 9,
          'subject' => 'Povýšení',
          'text' => 'Byl jsi povýšen na měšťana.',
          'from' => 1,
          'to' => 3,
          'created' => 1448473816,
          'read' => true,
        ],
        [
          'id' => 10,
          'subject' => 'Povýšení',
          'text' => 'Byl jsi povýšen na měšťana.',
          'from' => 1,
          'to' => 5,
          'created' => 1468075669,
          'read' => false,
        ],
      ])
      ->update();
  }
}
?>