<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\Marriage;
use Nexendrie\Orm\Marriage as MarriageEntity;

/**
 * CloseWeddingsTask
 *
 * @author Jakub Konečný
 */
final class CloseWeddingsTask {
  use \Nette\SmartObject;

  protected \Nexendrie\Orm\Model $orm;
  protected Marriage $marriageModel;
  
  public function __construct(\Nexendrie\Orm\Model $orm, Marriage $marriageModel) {
    $this->orm = $orm;
    $this->marriageModel = $marriageModel;
  }
  
  /**
   * @cronner-task Close weddings
   * @cronner-period 1 hour
   */
  public function run(): void {
    echo "Starting closing weddings ...\n";
    $weddings = $this->orm->marriages->findOpenWeddings();
    foreach($weddings as $wedding) {
      if(!$this->marriageModel->canFinish($wedding)) {
        echo "Wedding (#$wedding->id) cannot be finished!\n";
        continue;
      }
      echo "Closed wedding (#$wedding->id).\n";
      $wedding->status = MarriageEntity::STATUS_ACTIVE;
      $this->orm->marriages->persist($wedding);
    }
    $this->orm->flush();
    echo "Finished closing weddings ...\n";
  }
}
?>