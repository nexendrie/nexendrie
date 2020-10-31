<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class EventTransformer extends BaseTransformer {
  protected array $fields = [
    "id", "name", "description", "startAt", "endAt", "adventuresBonus", "workBonus", "prayerLifeBonus",
    "trainingDiscount", "repairingDiscount", "shoppingDiscount", "adventures",
  ];
  protected array $fieldsRename = ["startAt" => "start", "endAt" => "end", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\Event::class;
  }
}
?>