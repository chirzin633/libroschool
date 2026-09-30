<?php

namespace App\Enums;

enum UserRole: string
{
  case Pustakawan = 'Pustakawan';
  case Staff = 'Staff';

  public function label(): string
  {
    return match ($this) {
      self::Pustakawan => 'Pustakawan',
      self::Staff => 'Staff Perpustakaan'
    };
  }
}
