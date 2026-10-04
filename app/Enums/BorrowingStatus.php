<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BorrowingStatus: string implements HasLabel, HasColor
{
  case Borrowed = 'Borrowed';
  case Returned = 'Returned';

  public function label()
  {
    return match ($this) {
      self::Borrowed => 'Sedang Dipinjam',
      self::Returned => 'Telah dikembalikan'
    };
  }

  public function color()
  {
    return match ($this) {
      self::Borrowed => 'primary',
      self::Returned => 'gray'
    };
  }

  public function getLabel(): string
  {
    return match ($this) {
      self::Borrowed => "Sedang Dipinjam",
      self::Returned => "Telah dikembalikan"
    };
  }

  public function getColor(): string|array|null
  {
    return match ($this) {
      self::Borrowed => 'primary',
      self::Returned => 'gray'
    };
  }
}
