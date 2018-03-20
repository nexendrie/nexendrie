<?php
declare(strict_types=1);

namespace Nexendrie\Chat;

interface IOrderChatControlFactory {
  public function create(): OrderChatControl;
}
?>