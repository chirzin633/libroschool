<?php

namespace App\Enums;

enum MemberStatus: string
{
  case Active = 'Active';
  case Inactive = 'Inactive';

  public function label()
  {
    return match ($this) {
      self::Active => 'Aktif',
      self::Inactive => 'Non-Aktif'
    };
  }

  public function color()
  {
    return match ($this) {
      self::Active => 'success',
      self::Inactive => 'danger'
    };
  }

  /**
   * Helper logika bisnis: Cek apakah member ini boleh meminjam?
   * Hanya member ACTIVE yang bisa meminjam.
   */

  public function canBorrow(): bool
  {
    return $this === self::Active;
  }
}
