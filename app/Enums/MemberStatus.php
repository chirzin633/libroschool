<?php

namespace App\Enums;

enum MemberStatus: string
{
  case Active = 'Active';
  case Inactice = 'Inactive';

  public function label()
  {
    return match ($this) {
      self::Active => 'Aktif',
      self::Inactice => 'Non-Aktif'
    };
  }

  public function color()
  {
    return match ($this) {
      self::Active => 'success',
      self::Inactice => 'danger'
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
