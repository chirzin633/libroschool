<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Akun')
                    ->columnSpan('full')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->required(),

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn(string $context) => $context === 'create')
                            ->dehydrated(fn(?string $state) => filled($state))
                            ->rule(Password::min(8)->mixedCase()->numbers())
                            ->helperText('Minimal 8 karakter, huruf besar+kecil, dan angka. Kosongkan jika tidak ingin mengubah password.'),

                        Select::make('role')
                            ->label('Role')
                            ->enum(UserRole::class)
                            ->options(UserRole::class)
                            ->default(UserRole::Staff)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Akun Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk mencegah login tanpa menghapus data.'),
                    ])

            ]);
    }
}
