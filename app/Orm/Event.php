<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;

/**
 * Event
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $start
 * @property-read string $startAt {virtual}
 * @property int $end
 * @property-read string $endAt {virtual}
 * @property int $adventuresBonus {default 0}
 * @property int $workBonus {default 0}
 * @property int $prayerLifeBonus {default 0}
 * @property int $trainingDiscount {default 0}
 * @property int $repairingDiscount {default 0}
 * @property int $shoppingDiscount {default 0}
 * @property int $created
 * @property int $updated
 * @property OneHasMany|Adventure[] $adventures {1:m Adventure::$event}
 * @property-read bool $active {virtual}
 */
final class Event extends BaseEntity
{
    private \Nexendrie\Model\Locale $localeModel;

    public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void
    {
        $this->localeModel = $localeModel;
    }

    protected function getterStartAt(): string
    {
        return $this->localeModel->formatDateTime($this->start);
    }

    protected function setterEnd(int $value): int
    {
        if ($value < $this->start) {
            return $this->start;
        }
        return $value;
    }

    protected function getterEndAt(): string
    {
        return $this->localeModel->formatDateTime($this->end);
    }

    protected function setterAdventuresBonus(int $value): int
    {
        return Numbers::range($value, 0, 999);
    }

    protected function setterWorkBonus(int $value): int
    {
        return Numbers::range($value, 0, 999);
    }

    protected function setterPrayerLifeBonus(int $value): int
    {
        return Numbers::range($value, 0, 999);
    }

    protected function setterTrainingDiscount(int $value): int
    {
        return Numbers::range($value, 0, 100);
    }

    protected function setterRepairingDiscount(int $value): int
    {
        return Numbers::range($value, 0, 100);
    }

    protected function setterShoppingDiscount(int $value): int
    {
        return Numbers::range($value, 0, 100);
    }

    protected function getterActive(): bool
    {
        $time = time();
        return ($this->start <= $time && $this->end >= $time);
    }

    public function dummy(): EventDummy
    {
        return new EventDummy($this);
    }

    public function dummyArray(): array
    {
        return $this->dummy()->toArray();
    }
}
