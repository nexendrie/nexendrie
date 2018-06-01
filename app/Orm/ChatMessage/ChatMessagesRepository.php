<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ChatMessagesRepository
 *
 * @author Jakub Konečný
 */
final class ChatMessagesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [ChatMessage::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?ChatMessage {
    return $this->getBy(["id" => $id]);
  }
}
?>