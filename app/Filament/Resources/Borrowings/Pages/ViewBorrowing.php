<?php

namespace App\Filament\Resources\Borrowings\Pages;

use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Filament\Resources\Borrowings\Schemas\BorrowingViewSchema;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Override;

class ViewBorrowing extends ViewRecord
{
  protected static string $resource = BorrowingResource::class;

  #[Override]
  public function form(Schema $schema): Schema
  {
    return BorrowingViewSchema::configure($schema);
  }
}
