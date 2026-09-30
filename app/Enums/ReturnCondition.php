<?php

namespace App\Enums;

enum ReturnCondition: string
{
  case Good = 'Good';
  case Damaged = 'Damaged';
  case Lost = 'Lost';

  public function label()
  {
    return match ($this) {
      self::Good => 'Baik',
      self::Damaged => 'Rusak',
      self::Lost => 'Hilang'
    };
  }

  public function color()
  {
    return match ($this) {
      self::Good => 'success',
      self::Damaged => 'warning',
      self::Lost => 'danger'
    };
  }

  public function calculateNewStock(int $currentStock, int $currentAvailable)
  {
    return match ($this) {
      // GOOD: available +1, stock tetap
      self::Good => [
        'new_stock' => $currentStock,
        'new_available' => $currentAvailable + 1
      ],

      // DAMAGED: available +1 (fisik ada tapi rusak), stock tetap
      // Note: Spec says "DAMAGED tetap kembali ke stok tersedia". 
      // Jika ingin memisahkannya, biasanya dibuat status khusus atau manual edit stock.
      // Sesuai tabel di spec: DAMAGED -> available +1, stock tetap.
      self::Damaged => [
        'new_stock' => $currentStock,
        'new_available' => $currentAvailable + 1,
      ],

      // LOST: available tetap (karena tidak dikembalikan), stock -1
      self::Lost => [
        'new_stock' => max(0, $currentStock - 1), // Prevent negative
        'new_available' => $currentAvailable,
      ],
    };
  }

  /**
   * Cek apakah kondisi ini memicu denda keterlambatan?
   *  "LOST: denda keterlambatan tidak dihitung"
   */

  public function appliesLateFine(bool $isLate): bool
  {
    if (!$isLate) return false;

    return match ($this) {
      self::Good, self::Damaged => true,
      self::Lost => false // Hilang tidak kena denda telat, hanya denda hilang
    };
  }
}
