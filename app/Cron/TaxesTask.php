<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * TaxesTask
 *
 * @author Jakub Konečný
 */
final class TaxesTask extends BaseMonthlyCronTask {
  protected \Nexendrie\Model\Taxes $taxesModel;
  
  public function __construct(\Nexendrie\Model\Taxes $taxesModel) {
    $this->taxesModel = $taxesModel;
  }
  
  /**
   * @cronner-task(Taxes)
   * @cronner-period(1 day)
   * @cronner-time(00:00 - 01:00)
   */
  public function run(): void {
    $date = new \DateTime();
    $date->setTimestamp(time());
    if(!$this->isDue($date)) {
      return;
    }
    echo "Starting paying taxes ...\n";
    $result = $this->taxesModel->payTaxes();
    foreach($result as $town) {
      echo "Town (#$town->id) $town->name ...\n";
      foreach($town->denizens as $denizen) {
        echo "$denizen->publicname ";
        if($town->owner === $denizen->id) {
          echo "owns the town. He/she is not paying taxes.\n";
          continue;
        }
        echo "earned $denizen->income and will pay $denizen->tax to his/her liege.\n";
      }
    }
    echo "Finished paying taxes ...\n";
  }
}
?>