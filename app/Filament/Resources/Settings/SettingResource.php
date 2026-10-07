<?php

namespace App\Filament\Resources\Settings;

use App\Enums\UserRole;
use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\Schemas\SettingForm;
use App\Filament\Resources\Settings\Tables\SettingsTable;
use App\Models\Setting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Override;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;
    protected static ?int $navigationSort = 9;

    #[Override]
    public static function canAccess(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            // 'create' => CreateSetting::route('/create'),
            // 'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
