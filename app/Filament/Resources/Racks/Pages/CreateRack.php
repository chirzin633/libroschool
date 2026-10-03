<?php

namespace App\Filament\Resources\Racks\Pages;

use App\Filament\Resources\Racks\RackResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateRack extends CreateRecord
{
    protected static string $resource = RackResource::class;

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}