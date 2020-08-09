<?php
declare(strict_types=1);

namespace Nexendrie\Api\Transformers;

final class UserTransformer extends BaseTransformer {
  protected array $fields = [
    "id", "publicname", "createdAt", "lastActiveAt", "group", "gender", "banned", "town", "monastery", "castle",
    "guild", "guildRank", "order", "orderRank", "prayers", "title", "completedAdventures", "completedJobs",
    "producedBeers", "punishmentsCount", "lessonsTaken", "ownedTowns", "skills", "articles",
  ];
  protected array $fieldsRename = ["createdAt" => "created", "lastActiveAt" => "lastActive", "publicname" => "name", ];

  public function getEntityClassName(): string {
    return \Nexendrie\Orm\User::class;
  }
}
?>