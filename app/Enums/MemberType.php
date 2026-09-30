<?php

namespace App\Enums;

enum MemberType: string
{
  case Siswa = 'Siswa';
  case Guru = 'Guru';

  public function label()
  {
    return match ($this) {
      self::Siswa => 'Siswa',
      self::Guru => 'Guru'
    };
  }

  public function color()
  {
    return match ($this) {
      self::Siswa => 'primary',
      self::Guru => 'success'
    };
  }
}
