<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}