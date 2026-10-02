<?php

namespace App\Filament\Resources\Members\Pages;

use App\Enums\MemberStatus;
use App\Filament\Resources\Members\MemberResource;
use App\Services\CodeGeneratorService;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['member_code'] = CodeGeneratorService::generateMemberCode();

        // Set inactive_at jika status INACTIVE
        if (($data['status'] ?? null) === MemberStatus::Inactive && empty($data['inactive_at'])) {
            $data['inactive_at'] = now()->toDateString();
        }

        return $data;
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}