<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Models\Setting;
use App\Services\SettingService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Kunci Pengaturan')
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextInputColumn::make('value')
                    ->label('Nilai')
                    ->rules(['required', 'string'])
                    ->disabled()
                    ->sortable()
                    ->updateStateUsing(function (Setting $record, string $state) {
                        // Gunakan SettingService agar cache ter-invalidate otomatis
                        SettingService::set(
                            key: $record->key,
                            value: $state,
                            updatedBy: Auth::id()
                        );
                    }),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('updatedByUser.name')
                    ->label('Terakhir diubah oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Terakhir diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Ubah Pengaturan')
                    ->form([
                        TextInput::make('value')
                            ->label('Nilai Baru')
                            ->required()
                            ->helperText(fn(Setting $record) => "Nilai saat ini: {$record->value}")
                    ])
                    ->action(function (Setting $record, array $data) {
                        SettingService::set(
                            key: $record->key,
                            value: $data['value'],
                            updatedBy: Auth::id()
                        );
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Pengaturan berhasil diperbaharui')
                            ->seconds(2)
                    ),
            ])
            ->defaultSort('key');
    }
}
