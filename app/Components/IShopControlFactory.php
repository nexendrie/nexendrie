<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface IShopControlFactory {
  public function create(): ShopControl;
}
?>