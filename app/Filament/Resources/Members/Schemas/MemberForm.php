<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Enums\MemberStatus;
use App\Enums\MemberType;
use App\Enums\UserRole;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class MemberForm
{

    public static function canAccess()
    {
        return Auth::check();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Anggota')
                    ->columns(1)
                    ->schema([
                        TextInput::make('member_code')
                            ->label('Kode Anggota')
                            ->readOnly()
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->markAsRequired()
                            ->rule(['required', 'min:5', 'max:255']),
                        Select::make('type')
                            ->label('Tipe')
                            ->enum(MemberType::class)
                            ->options(MemberType::class)
                            ->markAsRequired()
                            ->rule(['required']),
                        TextInput::make('class_position')
                            ->label('Kelas / Jabatan')
                            ->markAsRequired()
                            ->rule(['required', 'min:5', 'max:100'])
                            ->placeholder('Contoh: XII IPA 1 atau Guru Matematika'),
                        Radio::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->markAsRequired()
                            ->rule(['required'])
                            ->inline(),
                        TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->markAsRequired()
                            ->rule(['required', 'min:8', 'max:20']),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->markAsRequired()
                            ->rule(['required'])
                            ->rows(3),
                        FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->directory('members')
                            ->maxSize(2048),
                    ]),
                Section::make('Status Keanggotaan')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('registered_at')
                            ->label('Tanggal Daftar')
                            ->required()
                            ->default(now()->toDateString())
                            ->native(false),
                        Select::make('status')
                            ->label('Status')
                            ->enum(MemberStatus::class)
                            ->options(MemberStatus::class)
                            ->required()
                            ->default(MemberStatus::Active)
                            ->disabled(fn(): bool => Auth::user()?->role !== UserRole::Pustakawan)
                            ->helperText('Hanya Pustakawan yang dapat mengubah status anggota.'),
                        DatePicker::make('inactive_at')
                            ->label('Tanggal Nonaktif')
                            ->native(false)
                            ->visible(fn(Get $get): bool => $get('status') === MemberStatus::Inactive->value)
                            ->disabled(fn(): bool => Auth::user()?->role !== UserRole::Pustakawan)
                    ])
            ]);
    }
}