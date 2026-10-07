<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Override;

enum FineStatus: string implements HasLabel
{
  case Unpaid = 'Unpaid';
  case Paid = 'Paid';

  #[Override]
  public function getLabel(): string|Htmlable|null
  {
    return match ($this) {
      self::Unpaid => 'Belum Lunas',
      self::Paid => 'Lunas'
    };
  }

  public function color()
  {
    return match ($this) {
      self::Unpaid => 'danger',
      self::Paid => 'success'
    };
  }
}
