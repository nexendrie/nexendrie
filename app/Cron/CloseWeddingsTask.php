<?php
declare(strict_types=1);

namespace Nexendrie\Cron;

use Nexendrie\Model\Marriage;
use Nexendrie\Orm\Marriage as MarriageEntity;
use Nexendrie\Orm\Model as ORM;

/**
 * CloseWeddingsTask
 *
 * @author Jakub Konečný
 */
final readonly class CloseWeddingsTask
{
    public function __construct(private ORM $orm, private Marriage $marriageModel)
    {
    }

    /**
     * @cronner-task(Close weddings)
     * @cronner-period(1 hour)
     */
    public function run(): void
    {
        echo "Starting closing weddings ...\n";
        $weddings = $this->orm->marriages->findOpenWeddings();
        foreach ($weddings as $wedding) {
            if (!$this->marriageModel->canFinish($wedding)) {
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
