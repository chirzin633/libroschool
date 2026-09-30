<?php

namespace App\Enums;

enum FineType: string
{
  case Late = 'Late';
  case Damaged = 'Damaged';
  case Lost = 'Lost';

  public function label()
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
