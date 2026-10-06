<?php

namespace App\Filament\Resources\Borrowings\Pages;

use App\Enums\BorrowingStatus;
use App\Enums\ReturnCondition;
use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Models\Borrowing;
use App\Services\ReturnService;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Override;

class ProcessReturn extends Page implements HasForms
{
  use InteractsWithForms;

  protected static string $resource = BorrowingResource::class;

  #[Override]
  public function getView(): string
  {
    return 'filament.resources.borrowings.pages.process-return';
  }

  public Borrowing $record;
  public array $data = [];

  public function mount(int|string|Borrowing $record)
  {
    if ($record instanceof Borrowing) {
      $this->record = $record->loadMissing('details.book', 'member');
    } else {
      $this->record = Borrowing::with('details.book', 'member')->findOrFail($record);
    }

    if ($this->record->status === BorrowingStatus::Returned) {
      Notification::make()
        ->warning()
        ->title('Transaksi sudah dikembalikan')
        ->send();

      $this->redirect(BorrowingResource::getUrl('view', ['record' => $this->record]));
      return;
    }

    //Pre-fill seluruh form data di mount() setelah record tersedia
    $returnsData = [];
    foreach ($this->record->details as $detail) {
      $returnsData[] = [
        'borrowing_detail_id' => $detail->id,
        'book_title' => $detail->book?->title ?? '-',
        'condition' => ReturnCondition::Good->value,
        'notes' => null,
      ];
    }

    $this->data = [
      'borrowing_code' => $this->record->borrowing_code,
      'member_name' => $this->record->member?->name ?? '-',
      'due_at' => $this->record->due_at?->format('d M Y') ?? '-',
      'returns' => $returnsData,
    ];
  }

  public function form(Schema $schema): Schema
  {
    $defaultReturns = [];

    foreach ($this->record->details as $detail) {
      $defaultReturns[] = [
        'borrowing_detail_id' => $detail->id,
        'book_title' => $detail->book?->title ?? '-',
        'condition' => ReturnCondition::Good->value,
        'notes' => null,
      ];
    }
    return $schema
      ->components([
        Section::make('Informasi Peminjaman')
          ->columns(3)
          ->schema([
            TextInput::make('borrowing_code')
              ->label('Kode Transaksi')
              ->dehydrated(false)
              ->readOnly()
              ->formatStateUsing(fn() => $this->record->borrowing_code),

            TextInput::make('member_name')
              ->label('Anggota')
              ->dehydrated(false)
              ->readOnly()
              ->formatStateUsing(fn() => $this->record->member?->name ?? '-'),

            TextInput::make('due_at')
              ->label('Jatuh Tempo')
              ->dehydrated(false)
              ->readOnly()
              ->formatStateUsing(fn() => $this->record->due_at?->format('d M Y') ?? '-'),
          ]),

        Section::make('Kondisi Buku yang Dikembalikan')
          ->description('Isi kondisi untuk SEMUA buku sebelum memproses pengembalian.')
          ->schema([
            Repeater::make('returns')
              ->hiddenLabel()
              ->addable(false)
              ->deletable(false)
              ->reorderable(false)
              ->schema([
                TextInput::make('book_title')
                  ->label('Buku')
                  ->readOnly()
                  ->dehydrated(false)
                  ->columnSpanFull(),

                Select::make('condition')
                  ->label('Kondisi')
                  ->enum(ReturnCondition::class)
                  ->options(ReturnCondition::class)
                  ->required(),

                Textarea::make('notes')
                  ->label('Catatan Kondisi')
                  ->rows(2)
                  ->maxLength(500),
              ])
              ->columns(2),
          ]),
      ])
      ->statePath('data');
  }

  public function processReturnAction(): Action
  {
    return Action::make('processReturn')
      ->label('Proses Pengembalian')
      ->color('primary')
      ->requiresConfirmation()
      ->modalHeading('Konfirmasi Pengembalian')
      ->modalDescription('Pastikan semua kondisi buku sudah terisi dengan benar. Proses ini tidak dapat dibatalkan.')
      ->action(function () {
        try {
          $formdata = $this->form->getState();

          $returnsData = [];
          foreach ($formdata['returns'] as $item) {
            $returnsData[$item['borrowing_detail_id']] = [
              'condition' => $item['condition'],
              'notes' => $item['notes'] ?? null
            ];
          }

          ReturnService::processBatchReturn(
            borrowingId: $this->record->id,
            returnsData: $returnsData,
            staffId: Auth::id()
          );

          Notification::make()
            ->success()
            ->title('Pengembalian berhasil diproses')
            ->send();

          $this->redirect(BorrowingResource::getUrl('view', ['record' => $this->record]));
        } catch (ValidationException $e) {
          throw $e;
        } catch (\Throwable $e) {
          Notification::make()
            ->danger()
            ->title('Gagal memproses pengembalian')
            ->body($e->getMessage())
            ->send();
        }
      });
  }

  #[Override]
  protected function getHeaderActions(): array
  {
    return [$this->processReturnAction()];
  }

  #[Override]
  public static function canAccess(array $parameters = []): bool
  {
    return Auth::check();
  }
}
