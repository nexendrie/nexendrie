<?php
declare(strict_types=1);

namespace Nexendrie\Components;

interface ISocialIcon {
  public function getLink(): string;
  public function getImage(): string;
  public function getImageAlt(): string;
  public function getImageTitle(): string;
}
?>