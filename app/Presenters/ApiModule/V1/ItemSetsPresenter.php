<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\ApiModule\V1;

/**
 * ItemSetsPresenter
 *
 * @author Jakub Konečný
 */
final class ItemSetsPresenter extends BasePresenter
{
    public function actionReadAll(): void
    {
        if (isset($this->params["associations"]) && count($this->params["associations"]) > 0) {
            return;
        }
        $records = $this->orm->itemSets->findAll();
        $this->sendCollection($records);
    }

    public function actionRead(): void
    {
        $record = $this->orm->itemSets->getById($this->getId());
        $this->sendEntity($record);
    }
}
