<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * BaseMonthlyCronTask
 *
 * @author Jakub Konečný
 */
abstract class BaseMonthlyCronTask {
  public function isDue(\DateTime $date): bool {
    return ((int) ($date->format("j")) === 1);
  }
}
?>