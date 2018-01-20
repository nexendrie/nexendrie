<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

/**
 * TaxesTask
 *
 * @author Jakub Konečný
 */
class TaxesTask {
  use \Nette\SmartObject;
  
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nexendrie\Model\Taxes */
  protected $taxesModel;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nexendrie\Model\Taxes $taxesModel) {
    $this->orm = $orm;
    $this->taxesModel = $taxesModel;
  }
  
  /**
   * @cronner-task Taxes
   * @cronner-period 1 day
   * @cronner-time 00:00 - 01:00
   */
  public function run(): void {
    $date = new \DateTime();
    $date->setTimestamp(time());
    if($date->format("j") != 1) {
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