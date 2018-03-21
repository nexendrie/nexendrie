<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

/**
 * ChatMessagesCollection
 *
 * @author Jakub Konečný
 */
class ChatMessagesCollection extends \Nexendrie\Utils\Collection {
  protected $class = ChatMessage::class;
}
?>