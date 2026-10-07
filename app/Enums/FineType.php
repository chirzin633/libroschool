<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Override;

enum FineType: string implements HasLabel
{
  case Late = 'Late';
  case Damaged = 'Damaged';
  case Lost = 'Lost';

  #[Override]
  public function getLabel(): string|Htmlable|null
  {
    return match ($this) {
      self::Late => 'Keterlambatan',
      self::Damaged => 'Kerusakan Buku',
      self::Lost => 'Kehilangan Buku'
    };
  }

  public function color()
  {
    return match ($this) {
      self::Late => 'info',
      self::Damaged => 'warning',
      self::Lost => 'danger'
    };
  }
}
