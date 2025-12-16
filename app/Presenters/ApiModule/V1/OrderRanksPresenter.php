<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * OrderRanksPresenter
 *
 * @author Jakub Konečný
 */
final class OrderRanksPresenter extends BasePresenter
{
    public function actionReadAll(): void
    {
        if (isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
            return;
        }
        $records = $this->orm->orderRanks->findAll();
        $this->sendCollection($records);
    }

    public function actionRead(): void
    {
        $record = $this->orm->orderRanks->getById($this->getId());
        $this->sendEntity($record);
    }
}
