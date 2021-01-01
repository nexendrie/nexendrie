<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface IOrderChatControlFactory extends IChatControlFactory {
  public function create(): OrderChatControl;
}
?>