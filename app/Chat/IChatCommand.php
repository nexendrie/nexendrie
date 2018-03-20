<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

/**
 * IChatCommand
 *
 * @author Jakub Konečný
 */
interface IChatCommand {
  public function getName(): string;
  public function setName(string $name): void;
  public function execute(): string;
}
?>