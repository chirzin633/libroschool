<?php

namespace App\Enums;

enum FineStatus: string
{
  case Unpaid = 'Unpaid';
  case Paid = 'Paid';

  public function label()
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
