<?php

namespace App\Enums;

enum BorrowingStatus: string
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
}
